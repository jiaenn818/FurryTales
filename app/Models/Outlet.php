<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Outlet extends Model
{
    // Table name
    protected $table = 'outlet';

    // Primary key
    protected $primaryKey = 'OutletID';

    // Disable auto-incrementing since OutletID is custom (prefix + number)
    public $incrementing = false;

    // Primary key type
    protected $keyType = 'string';

    // Enable timestamps
    public $timestamps = true;

    // Fillable fields for mass assignment
    protected $fillable = [
        'OutletID',
        'AddressLine1',
        'City',
        'PostCode',
        'State',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the pets associated with the outlet.
     */
    public function pets()
    {
        return $this->hasMany(Pet::class, 'OutletID', 'OutletID');
    }

    /**
     * Get the outlet_accessories records for this outlet.
     */
    public function outletAccessories()
    {
        return $this->hasMany(OutletAccessory::class, 'OutletID', 'OutletID');
    }

    /**
     * Get the accessories associated with the outlet via the join table.
     */
    public function accessories()
    {
        return $this->belongsToMany(Accessory::class, 'outlet_accessories', 'OutletID', 'AccessoryID')
                    ->withPivot('StockQty', 'Status')
                    ->withTimestamps();
    }

    // ==================== GETTERS & SETTERS ====================

    public function getOutletID()
    {
        return $this->OutletID;
    }

    public function setOutletID($OutletID)
    {
        $this->OutletID = $OutletID;
        return $this;
    }

    public function getPostCode()
    {
        return $this->PostCode;
    }

    public function setPostCode($PostCode)
    {
        $this->PostCode = $PostCode;
        return $this;
    }

    public function getState()
    {
        return $this->State;
    }

    public function setState($State)
    {
        $this->State = $State;
        return $this;
    }

    public function getAddressLine1()
    {
        return $this->AddressLine1;
    }

    public function setAddressLine1($AddressLine1)
    {
        $this->AddressLine1 = $AddressLine1;
        return $this;
    }

    public function getCity()
    {
        return $this->City;
    }

    public function setCity($City)
    {
        $this->City = $City;
        return $this;
    }

    // ==================== DATABASE METHODS ====================

    /**
     * Get all outlets ordered by ID.
     */
    public static function getAll()
    {
        return self::orderBy('OutletID')->get();
    }

    /**
     * Insert a new outlet.
     */
    public static function insertOutlet(array $data)
    {
        try {
            return self::create([
                'OutletID' => $data['OutletID'],
                'AddressLine1' => $data['AddressLine1'],
                'City' => $data['City'],
                'PostCode' => $data['PostCode'],
                'State' => $data['State'],
            ]);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
