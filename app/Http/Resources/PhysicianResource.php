<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PhysicianResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => data_get($this->resource, 'id'),
            'name' => data_get($this->resource, 'name'),
            'specialty' => data_get($this->resource, 'specialty'),
            'license_number' => data_get($this->resource, 'license_number'),
            'email' => data_get($this->resource, 'email'),
            'phone' => data_get($this->resource, 'phone'),
        ];
    }
}
