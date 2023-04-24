<x-mail::message>
# New Appointment Booking

Hello Paul,

There has been a new appointment booked at your garage. The customer appointment details are show below:

Name: {{ $name }},

Phone: {{ $phone }},

Email: {{ $email }},

Appointment Date: {{ $date }}

Appointment Time: {{ $start_at }}

Vehicle Make: {{ $vehicle_make }}

Vehicle Make: {{ $vehicle_model }}


Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
