<?php

namespace App\MetierAvancee\FaceId;

/**
 * DTO pour les données Face ID
 */
class FaceIdDto
{
    private string $email;
    private string $faceImageBase64;
    private ?string $faceIdToken = null;
    private bool $isValid = false;
    private ?float $confidence = null;
    private ?string $errorMessage = null;

    public function __construct(string $email, string $faceImageBase64)
    {
        $this->email = $email;
        $this->faceImageBase64 = $faceImageBase64;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFaceImageBase64(): string
    {
        return $this->faceImageBase64;
    }

    public function setFaceImageBase64(string $faceImageBase64): self
    {
        $this->faceImageBase64 = $faceImageBase64;
        return $this;
    }

    public function getFaceIdToken(): ?string
    {
        return $this->faceIdToken;
    }

    public function setFaceIdToken(string $faceIdToken): self
    {
        $this->faceIdToken = $faceIdToken;
        return $this;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;
        return $this;
    }

    public function getConfidence(): ?float
    {
        return $this->confidence;
    }

    public function setConfidence(float $confidence): self
    {
        $this->confidence = $confidence;
        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }
}
