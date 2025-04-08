<x-mail::message>
# Introduction

Thanks for Enrolling into the {{$enrollment->course->title}}

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
