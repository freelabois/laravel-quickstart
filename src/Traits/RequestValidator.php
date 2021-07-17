<?php


namespace Freelabois\LaravelQuickstart\Traits;


trait RequestValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        switch ($this->method()) {
            case 'POST':
                return optional($this)->post_rules ?? [];
            case 'PUT':
            case 'PATCH':
                return optional($this)->put_rules ?? [];
            default:
                return optional($this)->default_rules ?? [];
        }
    }
}
