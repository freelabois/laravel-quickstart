<?php


namespace Freelabois\LaravelQuickstart\Traits;


trait RequestValidator
{
    protected array $post_rules = [];
    protected array $put_rules = [];
    protected array $default_rules = [];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        switch ($this->method()) {
            case 'POST':
                return $this->post_rules;
            case 'PUT':
            case 'PATCH':
                return $this->put_rules;
            default:
                return $this->default_rules;
        }
    }
}
