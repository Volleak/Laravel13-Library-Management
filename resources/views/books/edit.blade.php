@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-white">Edit Book: {{ $book->title }}</h2>
            <a href="{{ route('books.index') }}" class="btn btn-outline-secondary btn-sm">Back to Catalog</a>
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

        <div class="card bg-dark text-white border-secondary">
            <div class="card-body">
                <form action="{{ route('books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
                    <form action="{{ route('books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label text-light">Book Title</label>
                                <input type="text" name="title"
                                    class="form-control bg-dark text-white border-secondary" value="{{ $book->title }}"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-light">Author</label>
                                <select id="author-select" name="author_ids[]" multiple required>
                                    @foreach ($authors as $author)
                                        <option value="{{ $author->id }}"
                                            {{ in_array($author->id, $selectedAuthors ?? []) ? 'selected' : '' }}>
                                            {{ $author->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label text-light">ISBN</label>
                                <input type="text" name="isbn"
                                    class="form-control bg-dark text-white border-secondary" value="{{ $book->isbn }}"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-light">Category</label>
                                <select name="category_id" class="form-select bg-dark text-white border-secondary" required>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $book->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-light">Cover Image</label>
                            <input type="file" name="cover_image"
                                class="form-control bg-dark text-white border-secondary"
                                accept="image/jpeg, image/png, image/jpg, image/webp">
                            <div class="form-text text-muted small">Supported formats: JPG, PNG, WEBP. Leave blank to keep
                                current image.</div>

                            @if ($book->cover_path)
                                <div class="mt-3">
                                    <span class="d-block text-muted mb-2 small">Current Image:</span>
                                    <img src="{{ asset('storage/' . $book->cover_path) }}" alt="Cover"
                                        class="rounded border border-secondary shadow-sm"
                                        style="height: 120px; object-fit: cover;">
                                </div>
                            @endif
                        </div>

                        <div class="pt-2 border-top border-secondary">
                            <button type="submit" class="btn btn-primary mt-3 px-4">Update Book</button>
                        </div>
                    </form>
            </div>
        </div>
    </div>


    <!-- Choices.js Script & Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <style>
        /* Dark Theme overrides for Choices.js */
        .choices__inner {
            background-color: #0b1121 !important;
            border: 1px solid #1e293b !important;
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
        }

        .choices__input--cloned {
            min-width: 200px !important;
            width: auto !important;
        }

        .choices__list--dropdown {
            background-color: #0b1121 !important;
            border: 1px solid #1e293b !important;
            color: #fff;
        }

        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: #1e293b !important;
            color: #fff !important;
        }

        .choices__list--multiple .choices__item {
            background-color: #3b82f6 !important;
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
