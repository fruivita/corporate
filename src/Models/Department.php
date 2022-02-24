<?php

namespace FruiVita\Corporate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Lotação de uma determinada pessoa.
 *
 * @see https://laravel.com/docs/8.x/eloquent
 */
class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $fillable = ['id', 'parent_department', 'name', 'acronym'];

    public $incrementing = false;

    /**
     * Lotação pai de uma determinada lotação.
     */
    public function parentDepartment()
    {
        return $this->belongsTo(Department::class, 'parent_department', 'id');
    }

    /**
     * Lotações filhas de uma determinada lotação.
     */
    public function childDepartments()
    {
        return $this->hasMany(Department::class, 'parent_department', 'id');
    }

    /**
     * Pessoas lotadas em uma determinada lotação.
     */
    public function persons()
    {
        return $this->hasMany(Person::class, 'department_id', 'id');
    }
}
