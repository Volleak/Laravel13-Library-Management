@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Add New Book</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Book Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">Author</label>

                            <!-- REMOVED THE CLASS ATTRIBUTE ENTIRELY -->
                            <select id="author-select" name="author_ids[]" multiple required>
                                @foreach ($authors as $author)
                                    <option value="{{ $author->id }}">{{ $author->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">ISBN</label>
                            <input type="text" name="isbn" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="" disabled selected>Select a category...</option>

                                <!-- Loop through the categories from the database -->
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Cover Image</label>
                        <input type="file" name="cover_image" class="form-control"
                            accept="image/jpeg, image/png, image/jpg, image/webp">
                        <div class="form-text text-muted small">Supported formats: JPG, PNG, WEBP.</div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Book</button>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0 list-unstyled small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Include Choices.js CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <style>
        /* Dark Theme overrides for Choices.js */
        .choices__inner {
            background-color: #0b1121 !important;
            /* Matches your deep blue inputs */
            border: 1px solid #1e293b !important;
            /* Matches your subtle borders */
            border-radius: 0.375rem;
            min-height: 38px;
            padding-bottom: 0px !important;
        }

        .choices__input {
            color: #fff !important;
            background-color: transparent !important;
        }

        .choices__input::placeholder {
            color: #64748b !important;
            /* Muted placeholder text */
        }

        .choices__list--dropdown {
            background-color: #0b1121 !important;
            /* Matches your deep blue inputs */
            border: 1px solid #1e293b !important;
            color: #fff;
        }

        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: #1e293b !important;
            /* Hover state for dropdown items */
            color: #fff !important;
        }

        .choices__list--multiple .choices__item {
            background-color: #3b82f6 !important;
            /* Bright blue for the selected tags */
            border: none !important;
            border-radius: 4px;
            margin-bottom: 5px;
        }

        .choices[data-type*="select-multiple"] .choices__button {
            border-left: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const authorSelect = document.getElementById('author-select');

            if (authorSelect) {
                new Choices(authorSelect, {
                    removeItemButton: true,

                    // ADD THESE TWO LINES FOR THE PLACEHOLDER
                    placeholder: true,
                    placeholderValue: 'Choose Author...',

                    searchPlaceholderValue: 'Search for an author...',
                    itemSelectText: '',
                    shouldSort: false,
                });
            }
        });
    </script>

@endsection
