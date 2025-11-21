<?php

namespace App\Helpers;

use Intervention\Image\Facades\Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\User;

class MemberCardGenerator
{
    /**
     * Generate member card image
     *
     * @param User $user
     * @return string Path to generated card
     */
    public static function generate(User $user)
    {
        // Card dimensions (sama seperti contoh: sekitar 800x500px)
        $width = 800;
        $height = 500;

        // Create blank canvas
        $card = Image::canvas($width, $height, '#ffffff');

        // Add gradient background (blue-green gradient)
        // Simplified: just use solid blue for now
        $card->rectangle(0, 0, $width, $height, function ($draw) {
            $draw->background('#1e88e5');
        });

        // Add green accent at bottom left
        $card->rectangle(0, $height - 150, 250, $height, function ($draw) {
            $draw->background('#66bb6a');
        });

        // Add library logo/text (top left)
        $card->text('PERPUSTAKAAN NASIONAL', 80, 120, function($font) {
            $font->file(public_path('fonts/Arial.ttf')); // Or use default
            $font->size(24);
            $font->color('#ffffff');
            $font->align('left');
            $font->valign('top');
        });

        $card->text('REPUBLIK INDONESIA', 80, 155, function($font) {
            $font->file(public_path('fonts/Arial.ttf'));
            $font->size(20);
            $font->color('#ffffff');
            $font->align('left');
            $font->valign('top');
        });

        // Add member name (big, bottom left)
        $card->text(strtoupper($user->name), 80, $height - 230, function($font) {
            $font->file(public_path('fonts/Arial-Bold.ttf'));
            $font->size(36);
            $font->color('#1a1a1a');
            $font->align('left');
            $font->valign('top');
        });

        // Add NIK/Member ID (with barcode representation)
        if ($user->nik) {
            // Generate barcode
            $barcodeData = '*' . $user->nik . '*';
            $card->text($barcodeData, 80, $height - 130, function($font) {
                $font->file(public_path('fonts/LibreBarcode39-Regular.ttf')); // Barcode font
                $font->size(48);
                $font->color('#000000');
                $font->align('left');
                $font->valign('top');
            });

            // Add NIK text below barcode
            $card->text('* ' . chunk_split($user->nik, 4, ' ') . '*', 80, $height - 70, function($font) {
                $font->file(public_path('fonts/Arial.ttf'));
                $font->size(14);
                $font->color('#000000');
                $font->align('left');
                $font->valign('top');
            });
        }

        // Add "UMUM" badge (top right)
        $card->text('UMUM', $width - 150, 80, function($font) {
            $font->file(public_path('fonts/Arial-Bold.ttf'));
            $font->size(28);
            $font->color('#ffd700');
            $font->align('center');
            $font->valign('top');
        });

        // Add member photo (top right)
        if ($user->photo && file_exists(storage_path('app/public/' . $user->photo))) {
            $photo = Image::make(storage_path('app/public/' . $user->photo));
            $photo->fit(180, 220); // Resize to card size
            $card->insert($photo, 'top-right', $width - 200, 130);
        } else {
            // Default avatar if no photo
            $card->rectangle($width - 200, 130, $width - 20, 350, function ($draw) {
                $draw->background('#cccccc');
            });
            $card->text(strtoupper(substr($user->name, 0, 1)), $width - 110, 230, function($font) {
                $font->file(public_path('fonts/Arial-Bold.ttf'));
                $font->size(72);
                $font->color('#666666');
                $font->align('center');
                $font->valign('middle');
            });
        }

        // Add validity date (bottom right)
        $validUntil = now()->addYears(3)->format('Y-m-d');
        $card->text('Masa Berlaku Kartu', $width - 200, $height - 120, function($font) {
            $font->file(public_path('fonts/Arial.ttf'));
            $font->size(14);
            $font->color('#1a1a1a');
            $font->align('left');
            $font->valign('top');
        });

        $card->text('Seumur Hidup', $width - 200, $height - 90, function($font) {
            $font->file(public_path('fonts/Arial-Bold.ttf'));
            $font->size(16);
            $font->color('#1a1a1a');
            $font->align('left');
            $font->valign('top');
        });

        // Add Pusnas logo placeholders (simplified)
        // You can add actual logos here

        // Save card
        $filename = 'member_cards/' . $user->id . '_' . time() . '.png';
        $path = storage_path('app/public/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $card->save($path, 90);

        return $filename;
    }
}
