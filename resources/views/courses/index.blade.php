@extends('components.layouts')

@section('content')
    <h1>Available Courses</h1>
    <ul>
        @foreach($courses as $course)
            <li>
                <a href="{{ route('courses.show', $course->id) }}">{{ $course->title }}</a>
            </li>
        @endforeach
    </ul>
@endsection
