<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\StoreCommentRequest;
use Illuminate\Validation\Validator;

/**
 * API variant of the existing StoreCommentRequest.
 *
 * It re-uses the exact same rules (name, email, content) used by the Blade
 * form and only adds a light anti-spam check on top. The Blade request class
 * is left untouched.
 */
class StorePostCommentRequest extends StoreCommentRequest
{
    /**
     * Maximum number of links allowed inside a single comment.
     */
    public const MAX_LINKS = 3;

    /**
     * Name of the honeypot field. Real visitors never see or fill it.
     */
    public const HONEYPOT_FIELD = 'website';

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $content = (string) $this->input('content', '');

            $links = preg_match_all('#(?:https?://|www\.)\S+#i', $content);

            if ($links !== false && $links > self::MAX_LINKS) {
                $validator->errors()->add(
                    'content',
                    'The content may not contain more than '.self::MAX_LINKS.' links.'
                );
            }
        });
    }

    /**
     * True when a bot filled in the hidden honeypot field.
     */
    public function isHoneypotTriggered(): bool
    {
        return filled($this->input(self::HONEYPOT_FIELD));
    }
}
