@extends('components.layouts')

@section('content')
    <h1>{{ $course->title }}</h1>
    <p>{{ $course->summary }}</p>
    <p>{{ $course->description }}</p>

    <!-- Enrollment form (only for authenticated users) -->
    @auth
        <form action="{{ route('courses.enroll', $course->id) }}" method="POST">
            @csrf
            <!-- Include any additional fields if needed -->
            <button type="submit">Enroll</button>
        </form>
    @else
        <p>Please <a href="{{ route('login') }}">login</a> to enroll.</p>
    @endauth
@endsection
