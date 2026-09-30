<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $employeeId = $this->route('employee')?->id ?? $this->route('employee');
        return [
            'employee_id' => [
                'required',
                'string',
                'min:4',
                'max:8',
                Rule::unique('employees', 'employee_id')->ignore($employeeId),
            ],
            'name'              => 'required|string|max:50',
            'company_id'        => 'required|exists:companies,id',
            'department_id'     => 'nullable|exists:departments,id',
            'designation_id'    => 'nullable|exists:designations,id',
            'sensor_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employees', 'sensor_id')->ignore($employeeId),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->ignore($employeeId),
            ],
            'phone'             => 'required|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',
        ];
    }
}
