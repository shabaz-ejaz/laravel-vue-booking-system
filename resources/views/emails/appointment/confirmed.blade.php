<x-mail::message>
# Appointment Confirmed

Hello {{ $name }},

Your appointment with Hayden's Garage has been confirmed. Your appointment details are show below:

Appointment Date: {{ $date }}

Appointment Time: {{ $start_at }}

Vehicle Make: {{ $vehicle_make }}

Vehicle Make: {{ $vehicle_model }}


Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
