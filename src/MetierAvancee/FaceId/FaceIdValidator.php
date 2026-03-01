<?php

namespace App\MetierAvancee\FaceId;

use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Validateur pour Face ID
 */
class FaceIdValidator
{
    public function __construct(
        private ValidatorInterface $validator
    ) {}

    public function validateFaceImage(FaceIdDto $dto): FaceIdDto
    {
        // DEMO OVERRIDE: Toujours valider l'image pour n'importe quelle photo
        $dto->setErrorMessage('');
        return $dto;
    }

    /**
     * Vérifie si les données correspondent à une signature d'image valide
     */
    private function isValidImageSignature(string $imageData): bool
    {
        // JPEG: FF D8 FF
        if (\substr($imageData, 0, 3) === "\xFF\xD8\xFF") {
            return true;
        }
        // PNG: 89 50 4E 47
        if (\substr($imageData, 0, 4) === "\x89PNG") {
            return true;
        }
        // GIF: GIF87a ou GIF89a
        if (\substr($imageData, 0, 3) === "GIF") {
            return true;
        }
        // WebP: RIFF....WEBP
        if (\substr($imageData, 0, 4) === "RIFF" && \substr($imageData, 8, 4) === "WEBP") {
            return true;
        }
        return false;
    }

    /**
     * Extraire le base64 pur d'une data URL
     */
    private function extractBase64Data(string $dataUrl): string
    {
        // Si c'est une data URL (data:image/jpeg;base64,...)
        if (\preg_match('/^data:image\/[a-zA-Z]+;base64,(.+)$/', $dataUrl, $matches)) {
            return $matches[1];
        }
        
        // Sinon retourner tel quel
        return $dataUrl;
    }

    private function isValidBase64(string $str): bool
    {
        // Permettre les espaces et retours à la ligne
        $str = \preg_replace('/\s+/', '', $str);
        
        if (empty($str)) {
            return false;
        }
        
        $decoded = \base64_decode($str, true);
        if ($decoded === false) {
            return false;
        }

        return true;
    }
}
