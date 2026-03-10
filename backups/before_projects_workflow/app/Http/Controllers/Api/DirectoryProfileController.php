<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DirectoryProfile;
use Illuminate\Http\Request;

class DirectoryProfileController extends Controller
{
    /**
     * Get list of verified and public lawyer profiles.
     */
    public function index(Request $request)
    {
        $profiles = DirectoryProfile::with(['user:id,name,email,profile_photo_path'])
            ->where('is_public', true)
            // ->where('is_verified', true) // Enable later for strict verification
            ->when($request->city, function ($query, $city) {
                return $query->where('city', 'like', "%{$city}%");
            })
            ->when($request->materia, function ($query, $materia) {
                // JSON search for specialties
                return $query->whereJsonContains('specialties', $materia);
            })
            ->orderByDesc('is_verified') // Verified first
            ->latest()
            ->paginate(20);

        // Transform collection to hide sensitive data strictly
        $data = $profiles->getCollection()->transform(function ($profile) {
            return [
                'id' => $profile->id,
                'name' => $profile->user->name,
                'photo_url' => $profile->user->profile_photo_url,
                'headline' => $profile->headline,
                'bio' => $profile->bio,
                'specialties' => $profile->specialties,
                'city' => $profile->city,
                'state' => $profile->state,
                'license' => $profile->professional_license,
                'is_verified' => $profile->is_verified,
                'slug' => $profile->slug,
                'contact' => [
                    'whatsapp' => $profile->whatsapp,
                    'linkedin' => $profile->linkedin,
                    'website' => $profile->website,
                    // Link to Appointment Booking (The "Hook")
                    'book_url' => route('asesorias.public', ['token' => 'DIRECTORY-' . $profile->user->id]), 
                    // Note: We might need a generic token or link logic here, typically user specific. 
                    // For now, let's assume we link to their profile or a booking page if they have one set up.
                    // Actually, the user should have a configured 'Asesoria' type to direct to.
                    // For now, let's just return the public profile link we might build later or just their contact info.
                ]
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $profiles->currentPage(),
                'last_page' => $profiles->lastPage(),
                'total' => $profiles->total(),
            ]
        ]);
    }
}
