<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerAppointmentRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Mail\AppointmentConfirmed;
use App\Mail\AppointmentConfirmedAdmin;
use App\Models\Appointment;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class AppointmentController extends Controller
{

    /**
     * Get available time slots for a given chosen date
     * @param Request $request
     * @param $date
     * @return array
     */
    public function getAvailableSlots(Request $request, $date): array
    {
        $storeOpeningHours = config('app.store_hours');
        $availableSlots = [];


        if ($storeOpeningHours) {
            $parsedDate = Carbon::parse($date);

            // if we have the day of week in our opening hours array
            if ($parsedDate && $storeOpeningHours[$parsedDate->dayOfWeek]) {
                // get any slots already booked or blocked out for the given date
                $bookedSlots = Appointment::whereDate('date', $parsedDate)->get();


                // get the open and close time for the chosen date
                $openingTime = $storeOpeningHours[$parsedDate->dayOfWeek]['open'];
                $closingTime = $storeOpeningHours[$parsedDate->dayOfWeek]['close'];
                $duration = '30';

                $start_time = strtotime($openingTime);
                $end_time = strtotime($closingTime);

                $add_mins = $duration * 60;

                // loop through the results and create an array of available 30 minute time slots to send through to the UI
                while ($start_time <= $end_time) // loop between time
                {
                    $availableSlots[] = date("H:i:s", $start_time);
                    $start_time += $add_mins;
                }

                // remove any booked slots from available slots
                if (!$bookedSlots->isEmpty()) {
                    foreach ($bookedSlots as $slot) {

                        if (($key = array_search($slot->start_at, $availableSlots)) !== false) {
                            unset($availableSlots[$key]);
                        }
                    }
                }
            }
        }


        return $availableSlots;

    }

    /**
     * Create an appointment record
     * @param Request $request
     * @return RedirectResponse
     */
    public function createAppointment(CreateCustomerAppointmentRequest $request): RedirectResponse
    {
        // validate the request
        $validated = $request->validated();

        // parse and format the date and time slots with Carbon
        $start_at = Carbon::createFromFormat('H:i:s', $validated['start_at']);
        $end_at = $start_at->copy()->addMinutes(30);
        $parsedDate = Carbon::parse($validated['date']);


        $existingAppointments = Appointment::whereDate('date', $parsedDate)->where('start_at', $start_at->format('H:i:s'))->get();

        // check if there are any existing appointments for the given date and time slot
        if ($existingAppointments->isNotEmpty()) {
            // throw an error and don't create the appointment
             return back()->with('error', 'The selected date and time is currently unavailable');
        } else {
            // no appointments exist at this date and time so continue with creation
            $appointment = Appointment::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'vehicle_make' => $validated['vehicle_make'],
                'vehicle_model' => $validated['vehicle_model'],
                'date' => $parsedDate,
                'start_at' => $start_at->format('H:i:s'),
                'end_at' => $end_at->format('H:i:s'),
                'type' => 'customer',
            ]);

            // send customer confirmation email
            Mail::to($appointment->email)->send(new AppointmentConfirmed($appointment));


            // send business owner confirmation email
             Mail::to(config('mail.from.address'))->send(new AppointmentConfirmedAdmin($appointment));


             return back()->with('success','Your appointment has successfully been booked, you should shortly receive an email confirmation with your appointment details.');
        }


    }



    /**
     * Show the admin landing page with some records
     * @param Request $request
     * @return Response
     */
    public function admin(Request $request): Response
    {
        $appointments = Appointment::latest()->get();
        return Inertia::render('Dashboard', [
            'appointments' => $appointments
        ]);
    }
}
