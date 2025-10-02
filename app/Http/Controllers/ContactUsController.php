<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactUs;
use App\Models\SocialMedia;
use App\Http\Resources\ContactUsResource;
use App\Http\Resources\SocialMediaResource;
use Exception;

class ContactUsController extends Controller
{

    // store contact us
    public function update(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:255'],
                'social_media' => ['required', 'array'],
            ]);

            $contactUs = ContactUs::first();
            $contactUs->update([
                'name' => $request->name,
                'phone' => $request->phone,
            ]);

            if(isset($request->social_media)) { 
                SocialMedia::query()->delete();

                foreach($request->social_media as $socialMedia) {
                    SocialMedia::create([
                        'platform' => $socialMedia['platform'],
                        'url' => $socialMedia['url'],
                    ]);
                }
            }

            $socialMedia = SocialMedia::all();

            return response()->json([
                "status" => "success",
                "message" => "Contact us stored successfully",
                "data" => [
                    "contact_us" => new ContactUsResource($contactUs),
                    "social_media" => SocialMediaResource::collection($socialMedia)
                ],

            ], 201);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to store contact us",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    // index
    public function index()
    {
        try {
        $contactUs = ContactUs::first();
        $socialMedia = SocialMedia::all();
        return response()->json([
            "status" => "success",
            "data" => new ContactUsResource($contactUs),
                "social_media" => SocialMediaResource::collection($socialMedia)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to index contact us",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
