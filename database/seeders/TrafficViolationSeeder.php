<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TrafficViolation;
class TrafficViolationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $data = [
            ["code" => "1.a", "violation" => "Includes inappropriate or invalid driver's license", "fine" => "3000.00"],
            ["code" => "1.DD.A", "violation" => "Distracted Driving (RA 10913)", "fine" => 5000.00],
            ["code" => "1.e", "violation" => "Reckless Driving"],
            ["code" => "1.g.1", "violation" => "Failure to wear the prescribed seatbelt device and/or failure to require the front seat passenger to wear seatbelt"],
            ["code" => "1.h", "violation" => "Failure to wear the standard protective MC helmet or failure to require the back rider to wear standard protective MC helmet (R.A 10054)"],
            ["code" => "1.i", "violation" => "FAILURE TO CARRY DRIVER'S LICENSE, CERTIFICATE OF REGISTRATION OR OFFICIAL RECEIPT WHILE DRIVING A MOTOR VEHICLE"],
            ["code" => "1.j.1", "violation" => "Parking Violations"],
            ["code" => "1.j.2", "violation" => "Disregarding Traffic Signs"],
            ["code" => "1.j.3", "violation" => "Allowing passengers on top or cover of a motor vehicle"],
            ["code" => "1.j.5", "violation" => "Permitting passenger to ride on step board or mudguard of MV"],
            ["code" => "1.j.7", "violation" => "Driving in a place not intended for traffic or false parking"],
            ["code" => "1.j.8", "violation" => "Hitching or permitting a person or to hitch to a motor vehicle"],
            ["code" => "1.j.35", "violation" => "Obstructing the free passage of other vehicles"],
            ["code" => "1.j.37", "violation" => "Refusal to render service to the public"],
            ["code" => "1.j.38", "violation" => "Overcharging/Undercharging of fare"],
            ["code" => "1.j.39", "violation" => "No evidence of franchise presented during apprehension"],
            ["code" => "1.j.41", "violation" => "Operating the unit/s with defective parts"],
            ["code" => "1.j.45", "violation" => "No sign board"],
            ["code" => "1.j.46", "violation" => "Pick and Drop of Passengers outside the terminal"],
            ["code" => "1.j.49", "violation" => "Trip cutting"],
            ["code" => "1.j.51", "violation" => "Breach of franchise conditions"],
            ["code" => "2.a", "violation" => "Driving an unregistered Motor Vehicle"],
            ["code" => "2.b", "violation" => "Change in color and other unauthorized modifications" , "fine" => "10000.00"],
            ["code" => "2.d", "violation" => "MV operating with defective/improper/unauthorized accessories, devices, equipment and parts"],
            ["code" => "2.e", "violation" => "Failure to attach or improper attachment/tampering of MV license plates and/or third plate sticker."],
            ["code" => "2.f", "violation" => "Smoke Belching (Section 46, R.A. 8749)"],
            ["code" => "4.1.a", "violation" => "Private MV operating as a PUV without authority from the LTFRB"],
            ["code" => "4.1.b", "violation" => "PUV operating outside of its approved route or area"],
            ["code" => "4.1.c", "violation" => "PUV operating differently from its authorized denomination"],
            ["code" => "4.1.e", "violation" => "PUV with expired CPC and without a pending application (1)"],
            ["code" => "4.2", "violation" => "REFUSAL TO RENDER SERVICE TO THE PUBLIC OR CONVEY PASSENGER TO DESTINATION"],
            ["code" => "4.3", "violation" => "OVERCHARGING/UNDERCHARGING OF FARE"],
            ["code" => "4.5", "violation" => "NO FRANCHISE/CERTIFICATE OF PUBLIC CONVENIENCE OR EVIDENCE OF FRANCHISE PRESENTED DURING APPREHENSION OR CARRIED INSIDE THE MOTOR VEHICLE"],
            ["code" => "4.6", "violation" => "FRAUD AND FALSITIES SUCH AS PRESENTATION OF FAKE AND SPURIOUS CPC, OR/CR, PLATES, STICKERS AND TAGS"],
            ["code" => "4.7", "violation" => "EMPLOYING RECKLESS, INSOLENT, DISCOURTEOUS OR ARROGANT DRIVERS"],
            ["code" => "4.9", "violation" => "OPERATING THE UNIT/S WITH DEFECTIVE PARTS AND ACCESSORIES"],
            ["code" => "4.17", "violation" => "NO PANEL ROUTE (PUJ, PUB, UV)"],
            ["code" => "4.18", "violation" => "NO SIGN BOARD (PUJ, PUB, UV)"],
            ["code" => "4.19", "violation" => "PICK AND DROP OF PASSENGERS OUTSIDE THE TERMINAL (PUJ, PUB, UV)"],
            ["code" => "4.22", "violation" => "TRIP CUTTING (PUJ, PUB, UV)"],
            ["code" => "4.25", "violation" => "BREACH OF FRANCHISE CONDITIONS UNDER 2011 REVISED TERMS AND CONDITIONS OF CPC NOT OTHERWISE HEREIN PROVIDED."],
            ["code" => "WS", "violation" => "Wearing Slipper"],
            ["code" => "3.b", "violation" => "Axle Overloading"],
        ];

        foreach ($data as $item) {
            // Check if a TrafficViolation record with the same code already exists
            $existingViolation = TrafficViolation::where('code', $item['code'])->first();
        
            // If no record with the same code exists, create a new TrafficViolation record
            if (!$existingViolation) {
                TrafficViolation::create([
                    'code' => $item['code'],
                    'violation' => $item['violation'],
                ]);
            } else {
                // Skip the creation as the code already exists
                continue;
            }
        }
        $fine = [
            ["code" => "1.a", "fine" => 5000.00],
            ["code" => "1.a", "fine" => 3000.00],
            ["code" => "1.b", "fine" => 10000.00],
            ["code" => "1.c", "fine" => 10000.00],
            ["code" => "1.g.1", "fine" => 2000.00],
            ["code" => "1.g.2", "fine" => 3000.00],
            ["code" => "1.h", "fine" => 1500.00],
            ["code" => "1.i", "fine" => 1000.00],
            ["code" => "1.j.1", "fine" => 1000.00],
            ["code" => "1.j.2", "fine" => 1000.00],
            ["code" => "1.j.3", "fine" => 1000.00],
            ["code" => "1.j.5", "fine" => 1000.00],
            ["code" => "1.j.7", "fine" => 1000.00],
            ["code" => "1.j.8", "fine" => 1000.00],
            ["code" => "1.j.9", "fine" => 1000.00],
            ["code" => "1.j.10", "fine" => 1000.00],
            ["code" => "1.j.11", "fine" => 1000.00],
            ["code" => "1.j.12", "fine" => 1000.00],
            ["code" => "1.j.13", "fine" => 1000.00],
            ["code" => "1.j.14", "fine" => 1000.00],
            ["code" => "1.j.15", "fine" => 1000.00],
            ["code" => "1.j.16", "fine" => 1000.00],
            ["code" => "1.j.17", "fine" => 1000.00],
            ["code" => "1.j.18", "fine" => 1000.00],
            ["code" => "1.j.19", "fine" => 1000.00],
            ["code" => "1.j.21", "fine" => 1000.00],
            ["code" => "1.j.22", "fine" => 1000.00],
            ["code" => "1.j.23", "fine" => 1000.00],
            ["code" => "1.j.24", "fine" => 1000.00],
            ["code" => "1.j.25", "fine" => 1000.00],
            ["code" => "1.j.26", "fine" => 1000.00],
            ["code" => "1.j.27", "fine" => 1000.00],
            ["code" => "1.j.28", "fine" => 1000.00],
            ["code" => "1.j.29", "fine" => 1000.00],
            ["code" => "1.j.30", "fine" => 1000.00],
            ["code" => "1.j.31", "fine" => 1000.00],
            ["code" => "1.j.32", "fine" => 1000.00],
            ["code" => "1.j.33", "fine" => 1000.00],
            ["code" => "1.j.34", "fine" => 1000.00],
            ["code" => "1.j.35", "fine" => 1000.00],
            ["code" => "1.j.36", "fine" => 1000.00],
            ["code" => "1.j.37", "fine" => 1000.00],
            ["code" => "1.j.38", "fine" => 1000.00],
            ["code" => "1.j.39", "fine" => 1000.00],
            ["code" => "1.j.40", "fine" => 1000.00],
            ["code" => "1.j.41", "fine" => 1000.00],
            ["code" => "1.j.42", "fine" => 1000.00],
            ["code" => "1.j.43", "fine" => 1000.00],
            ["code" => "1.j.44", "fine" => 1000.00],
            ["code" => "1.j.45", "fine" => 1000.00],
            ["code" => "1.j.46", "fine" => 1000.00],
            ["code" => "1.j.47", "fine" => 1000.00],
            ["code" => "1.j.48", "fine" => 1000.00],
            ["code" => "1.j.49", "fine" => 1000.00],
            ["code" => "1.j.50", "fine" => 1000.00],
            ["code" => "1.j.51", "fine" => 1000.00],
            ["code" => "4.2", "fine" => 5000.00],
            ["code" => "4.3", "fine" => 1000.00],
            ["code" => "4.5", "fine" => 5000.00],
            ["code" => "4.6", "fine" => 1000.00],
            ["code" => "4.7", "fine" => 5000.00],
            ["code" => "4.9", "fine" => 5000.00],
            ["code" => "4.10", "fine" => 1000.00],
            ["code" => "4.13", "fine" => 1000.00],
            ["code" => "4.14", "fine" => 1000.00],
            ["code" => "4.17", "fine" => 5000.00],
            ["code" => "4.18", "fine" => 5000.00],
            ["code" => "4.19", "fine" => 5000.00],
            ["code" => "4.20", "fine" => 1000.00],
            ["code" => "4.21", "fine" => 1000.00],
            ["code" => "4.22", "fine" => 1000.00],
            ["code" => "4.23", "fine" => 1000.00],
            ["code" => "4.25", "fine" => 5000.00],
            ["code" => "4.1.a", "fine" => 1000000.00],
            ["code" => "4.1.b", "fine" => 200000.00],
            ["code" => "4.1.c", "fine" => 200000.00],
            ["code" => "4.1.e", "fine" => 200000.00],
            ["code" => "2.a", "fine" => 10000.00],
            ["code" => "2.b", "fine" => 5000.00],
            ["code" => "2.c", "fine" => 50000.00],
            ["code" => "2.d", "fine" => 5000.00],
            ["code" => "2.e", "fine" => 5000.00],
            ["code" => "2.f", "fine" => 2000.00],
            ["code" => "2.g", "fine" => 3000.00],
            ["code" => "2.h", "fine" => 2000.00],
            ["code" => "3.a", "fine" => 1000.00],
            ["code" => "3.c", "fine" => 1000.00],
        ];
        foreach ($fine as $xfine) {
            $violation = TrafficViolation::where('code', $xfine['code'])->first();
            if ($violation) {
                $violation->update(['fine' => $xfine['fine']]);
            }
        }
    }
}
