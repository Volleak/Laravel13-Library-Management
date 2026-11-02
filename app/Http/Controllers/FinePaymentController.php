<?php
namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\FinePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FinePaymentController extends Controller
{
    // Staff: Process a payment for a specific fine
    public function store(Request $request, Fine $fine)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.1',
            'payment_method' => 'required|string|in:cash,card,transfer'
        ]);

        try {
            return DB::transaction(function () use ($request, $fine) {
                // Lock the fine row to prevent race conditions during payment processing
                $fine = Fine::lockForUpdate()->find($fine->id);
                
                // Calculate the remaining balance safely
                $alreadyPaid = $fine->payments()->sum('amount');
                $remainingBalance = $fine->amount - $alreadyPaid;

                // Prevent overpayment
                if ($request->amount > $remainingBalance) {
                    throw new \Exception("Payment amount exceeds remaining balance of $" . number_format($remainingBalance, 2));
                }

                // Record the payment
                FinePayment::create([
                    'fine_id' => $fine->id,
                    'amount' => $request->amount,
                    'payment_date' => now(),
                    'payment_method' => $request->payment_method,
                    'processed_by_id' => Auth::id(), // Using Auth facade to keep your editor happy
                    'reference_number' => 'RCPT-' . strtoupper(Str::random(8))
                ]);

                // Determine the new status of the fine
                $newTotalPaid = $alreadyPaid + $request->amount;
                $newStatus = ($newTotalPaid >= $fine->amount) ? 'paid' : 'partial';

                // Update the Fine record
                $fine->update([
                    'status' => $newStatus
                ]);

                return back()->with('success', 'Payment processed successfully. Receipt generated.');
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}