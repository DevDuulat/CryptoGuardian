@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb mb-4">
            <div class="pull-left">
                <h2>Редактировать Вопрос</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('tests.index') }}"> Назад</a>
            </div>
        </div>
    </div>

    <div class="container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('image-quiz.update', ['test' => $test->id, 'imageQuiz' => $imageQuiz->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div id="question-container">
                <div class="question-item">
                    <div class="form-group">
                        <label for="question">Вопрос</label>
                        <input type="text" name="question" class="form-control" value="{{ old('question', $imageQuiz->question) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="images">Изображения</label>
                        <div class="image-upload-container">
                            @foreach ($imageQuiz->images as $index => $image)
                                <div class="input-group mb-3">
                                    <input type="file" name="images[]" class="form-control">
                                    @if ($index > 0 || count($imageQuiz->images) > 1)
                                        <button type="button" class="btn btn-danger btn-remove-image">Удалить</button>
                                    @endif
                                </div>
                                <div class="image-preview mt-3">
                                    <img src="{{ asset('storage/' . $image) }}" class="img-fluid mb-2" alt="Изображение">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="correct_sequence">Правильная последовательность (массив)</label>
                        <input type="text" name="correct_sequence" class="form-control correct-sequence" value="{{ old('correct_sequence', implode(',', $imageQuiz->correct_sequence)) }}" required>
                    </div>
                    <hr>
                </div>
            </div>
            <button type="button" id="add-question-btn" class="btn btn-primary mb-4">Добавить вопрос</button>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>

    <script>
        document.getElementById('add-question-btn').addEventListener('click', function() {
            const container = document.getElementById('question-container');
            const index = document.querySelectorAll('.question-item').length;
            const div = document.createElement('div');
            div.className = 'question-item';
            div.innerHTML = `
                <div class="form-group">
                    <label for="question">Вопрос</label>
                    <input type="text" name="questions[${index}][question]" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="images">Изображения</label>
                    <div class="image-upload-container">
                        <div class="input-group mb-3">
                            <input type="file" name="questions[${index}][images][]" class="form-control" required>
                            <button type="button" class="btn btn-danger btn-remove-image">Удалить</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary add-image-btn">Добавить изображение</button>
                    <div class="image-preview mt-3"></div>
                </div>
                <div class="form-group">
                    <label for="correct_sequence">Правильная последовательность (массив)</label>
                    <input type="text" name="questions[${index}][correct_sequence]" class="form-control correct-sequence" required>
                </div>
                <button type="button" class="btn btn-danger btn-remove-question">Удалить вопрос</button>
                <hr>
            `;
            container.appendChild(div);
        });

        document.getElementById('question-container').addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-question')) {
                e.target.closest('.question-item').remove();
                updateQuestionIndexes();
            } else if (e.target.classList.contains('btn-remove-image')) {
                const inputGroup = e.target.closest('.input-group');
                const preview = inputGroup.nextElementSibling;
                inputGroup.remove();
                preview.innerHTML = ''; // Очистить превью изображения при удалении
                updateCorrectSequenceInput();
                updateRemoveButtons();
            }
        });

        document.getElementById('question-container').addEventListener('change', function(e) {
            if (e.target.type === 'file') {
                const preview = e.target.closest('.form-group').querySelector('.image-preview');
                preview.innerHTML = '';
                Array.from(e.target.closest('.form-group').querySelectorAll('input[type="file"]')).forEach(input => {
                    if (input.files.length > 0) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.maxWidth = '150px';
                            img.style.margin = '10px';
                            preview.appendChild(img);
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                });
                updateCorrectSequenceInput();
            }
        });

        function updateCorrectSequenceInput() {
            document.querySelectorAll('.question-item').forEach((item, questionIndex) => {
                const images = item.querySelectorAll('input[type="file"]');
                const correctSequence = [];
                images.forEach((input, imageIndex) => {
                    if (input.files.length > 0) {
                        correctSequence.push(`${imageIndex + 1}`);
                    }
                });
                item.querySelector('.correct-sequence').value = correctSequence.join(',');
            });
        }

        function updateQuestionIndexes() {
            document.querySelectorAll('.question-item').forEach((item, index) => {
                item.querySelector('.form-group > label').textContent = `Вопрос ${index + 1}`;
            });
            updateRemoveButtons();
        }

        function updateRemoveButtons() {
            document.querySelectorAll('.question-item').forEach(item => {
                const removeButtons = item.querySelectorAll('.btn-remove-image');
                if (removeButtons.length > 1) {
                    removeButtons.forEach(btn => {
                        btn.style.display = 'inline-block';
                    });
                } else {
                    removeButtons.forEach(btn => {
                        btn.style.display = 'none';
                    });
                }
            });
        }
    </script>

@endsection
