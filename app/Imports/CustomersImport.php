<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Customer([
            'document_type'=>$row["tipo_documento"],
            'document_number'=>$row["documento"],
            'name'=>$row["nombre"],
            'type_sex_id'=>$row["sexo"],
            'age_range_id'=>$row["edad"],
            'telephone'=>$row["telefono"],
            'email'=>$row["correo"],
            'province_id'=>$row["provincia"],
            'district_id'=>$row["distrito"],
            'township_id'=>$row["corregimiento"],
        ]);
    }
}
