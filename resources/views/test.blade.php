<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Blade</title>
</head>
<body>
    @foreach($questions as $question)
        <p>{{ $question->question }}</p>
    @endforeach
</body>
</html>

