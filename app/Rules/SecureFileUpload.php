<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class SecureFileUpload implements ValidationRule
{
    /**
     * Allowed mime types
     */
    private const ALLOWED_MIME_TYPES = [
        // Images
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        // Documents
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        'text/csv',
    ];

    /**
     * Dangerous file extensions to block
     */
    private const BLOCKED_EXTENSIONS = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps',
        'exe', 'bat', 'cmd', 'com', 'pif', 'scr',
        'js', 'vbs', 'wsf', 'wsh',
        'sh', 'bash',
    ];

    /**
     * Maximum file size in bytes (default 10MB)
     */
    private int $maxSize;

    /**
     * Allowed mime types for this instance
     */
    private array $allowedTypes;

    public function __construct(?int $maxSize = null, ?array $allowedTypes = null)
    {
        $this->maxSize = $maxSize ?? 10485760; // 10MB default
        $this->allowedTypes = $allowedTypes ?? self::ALLOWED_MIME_TYPES;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            $fail('The :attribute must be a valid file.');
            return;
        }

        // Check file size
        if ($value->getSize() > $this->maxSize) {
            $maxSizeMB = round($this->maxSize / 1048576, 2);
            $fail("The :attribute must not exceed {$maxSizeMB}MB.");
            return;
        }

        // Check mime type
        $mimeType = $value->getMimeType();
        if (!in_array($mimeType, $this->allowedTypes)) {
            $fail('The :attribute file type is not allowed.');
            return;
        }

        // Check file extension
        $extension = strtolower($value->getClientOriginalExtension());
        if (in_array($extension, self::BLOCKED_EXTENSIONS)) {
            $fail('The :attribute file extension is not allowed for security reasons.');
            return;
        }

        // Validate file name (no path traversal)
        $fileName = $value->getClientOriginalName();
        if (preg_match('/\.\./', $fileName) || preg_match('/[\/\\\\]/', $fileName)) {
            $fail('The :attribute file name contains invalid characters.');
            return;
        }

        // Additional check: verify file is actually uploaded
        if (!$value->isValid()) {
            $fail('The :attribute file upload failed.');
            return;
        }
    }
}
