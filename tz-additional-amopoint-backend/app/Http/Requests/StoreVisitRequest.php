<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('country')) {
            $this->merge([
                'country' => strtoupper((string) $this->input('country')),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'visitor_id' => ['nullable', 'string', 'max:128'],
            'site_host' => ['nullable', 'string', 'max:255'],
            'page_title' => ['nullable', 'string', 'max:255'],
            'page_url' => ['nullable', 'url:http,https', 'max:2048'],
            'referrer' => ['nullable', 'url:http,https', 'max:2048'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'size:2'],
            'device_type' => ['nullable', 'string', Rule::in(['desktop', 'mobile', 'tablet', 'bot', 'unknown'])],
            'browser' => ['nullable', 'string', 'max:120'],
            'platform' => ['nullable', 'string', 'max:120'],
            'screen_width' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'screen_height' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'language' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:80'],
        ];
    }
}
