<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class GlobalHelper
{

    public static function daterange($var)
    {
        $dates = explode('-', $var);
        $startdate = Carbon::createFromDate($dates[0]);
        $enddate = Carbon::createFromDate($dates[1]);
        $range = $startdate->diffInDays($enddate);
        return $range;
    }

    // public static function  Permissions(){

    //         $routeCollection = Route::getRoutes()->get();
    //         $permissions = []; // Initialize an empty array for permissions

    //         foreach ($routeCollection as $item) {
    //             $name = $item->action;

    //             if (!empty($name['as'])) {
    //                 $permission = trim(strtolower($name['as']));
    //                 $permission = str_replace(['.index', '.add', '.store', '.edit', '.update', '.delete', '.single', 'show', 'destroy'], '', $permission);
    //                 $ignoreRoutesStartingWith = 'sanctum|livewire|ignition|verification|dashboard|password|logout|register|login|front|contact|listing|search|singcat|cities|test|filter|home|area.destroy|filament|storage';

    //                 // Remove specific segments
    //                 $permissionFilled = trim(str_replace("user management ", '', $permission));

    //                 // Check if the route should be ignored and if the permission is filled
    //                 if (preg_match("($ignoreRoutesStartingWith)", $permission) === 0 && !empty($permissionFilled)) {
    //                     // Ensure each permission is added only once
    //                     $permissions[$permissionFilled] = true;
    //                 }
    //             }
    //         }

    //         // Get the keys of the permissions array to return unique permission names
    //         return array_keys($permissions);

    //     // $array = array_unique($permission);
    //     // $quotedArray = array_map(function ($value) {
    //     //     settype($value, 'integer');
    //     //     // return gettype($value);
    //     // }, $permissions);
    //     // dd(array_unique($array));
    //     // dd($permissions);
    // }

    // public static function Permissions() {
    //     $routeCollection = Route::getRoutes()->get();
    //     $permissions = []; // Initialize an empty array for permissions
    //     $ignoredPermissions = []; // Initialize an array for ignored permissions

    //     // Define routes to ignore
    //     $ignoreRoutesStartingWith = 'sanctum|livewire|ignition|verification|dashboard|password|logout|register|login|front|contact|listing|search|singcat|cities|test|filter|home|area.destroy|filament|storage';

    //     foreach ($routeCollection as $item) {
    //         $name = $item->action;

    //         if (!empty($name['as'])) {
    //             $permission = trim(strtolower($name['as']));
    //             $originalPermission = $permission; // Keep original for debugging

    //             // Strip out common suffixes to get the unique permission name
    //             $permission = preg_replace('/\.(index|add|store|edit|update|delete|single|show|destroy|create|detail|conf-delete)$/', '', $permission);

    //             // Check if the route should be ignored
    //             if (preg_match("($ignoreRoutesStartingWith)", $permission) === 0) {
    //                 // If the permission is not empty, add it to the array
    //                 if (!empty($permission)) {
    //                     $permissions[$permission] = true; // Store as unique
    //                 }
    //             } else {
    //                 // Collect ignored permissions for debugging
    //                 $ignoredPermissions[] = $originalPermission;
    //             }
    //         } else {
    //             // Collect routes with no names for debugging
    //             $ignoredPermissions[] = 'No name found for action: ' . json_encode($item->action);
    //         }
    //     }

    //     // Get the keys of the permissions array to return unique permission names
    //     $uniquePermissions = array_keys($permissions);

    //     // Return both unique permissions and ignored permissions separately
    //     return [
    //         'permissions' => array_values(array_unique($uniquePermissions)), // Ensuring unique entries
    //         'ignored' => $ignoredPermissions, // Keep ignored routes intact
    //     ];
    // }

    public static function Permissions() {
        $routeCollection = Route::getRoutes()->get();
        $permissions = []; // Initialize an empty array for permissions

        // Define routes to ignore
        $ignoreRoutesStartingWith = 'sanctum|livewire|ignition|verification|dashboard|password|logout|register|login|front|contact|listing|search|singcat|cities|test|filter|home|area.destroy|filament|storage';

        foreach ($routeCollection as $item) {
            $name = $item->action;

            if (!empty($name['as'])) {
                $permission = trim(strtolower($name['as']));
                // Strip out common suffixes to get the unique permission name
                $permission = preg_replace('/\.(index|add|store|edit|update|delete|single|show|destroy|create|detail|conf-delete)$/', '', $permission);

                // Check if the route should be ignored
                if (preg_match("($ignoreRoutesStartingWith)", $permission) === 0) {
                    // If the permission is not empty, add it to the array
                    if (!empty($permission)) {
                        $permissions[$permission] = true; // Store as unique
                    }
                }
            }
        }

        // Get the keys of the permissions array to return unique permission names
        return array_values(array_unique(array_keys($permissions))); // Return only unique permissions
    }






   public static function  fts_upload_img($img_file,$folder_name)
    {
        $imgpath = 'business/'.$folder_name;
        File::makeDirectory($imgpath, $mode = 0777, true, true);
        $imgDestinationPath = $imgpath.'/';
        $file_name = time()."_".$img_file->getClientOriginalName();
        $success = $img_file->move($imgDestinationPath, $file_name);
        return($file_name);
     }

     public static function  fts_upload_report($img_file,$folder_name)
     {
         $imgpath = 'reports/'.$folder_name;
         File::makeDirectory($imgpath, $mode = 0777, true, true);
         $imgDestinationPath = $imgpath.'/';
         $file_name = time()."_".$img_file->getClientOriginalName();
         $success = $img_file->move($imgDestinationPath, $file_name);
         return($file_name);
      }

   public static function  delete_report($img_file,$folder_name)
    {
        $imgpath = 'reports/'.$folder_name;
        File::makeDirectory($imgpath, $mode = 0777, true, true);
        $imgDestinationPath = $imgpath.'/';
        $old_image=$imgDestinationPath.$img_file;
        if (File::exists($old_image))
        {
            File::delete($old_image);
            return true;
        }
        else
        {
            return false;
        }
    }
   public static function  delete_img($img_file,$folder_name)
    {
        $imgpath = 'business/'.$folder_name;
        File::makeDirectory($imgpath, $mode = 0777, true, true);
        $imgDestinationPath = $imgpath.'/';
        $old_image=$imgDestinationPath.$img_file;
        if (File::exists($old_image))
        {
            File::delete($old_image);
            return true;
        }
        else
        {
            return false;
        }
    }
    public static function holidays_count($holiday)
    {
        // $holiday = $this->holiday;
        // dd($holiday);
        $sum = $holiday->jan + $holiday->feb + $holiday->mar + $holiday->apr + $holiday->may + $holiday->jun + $holiday->july + $holiday->aug + $holiday->sep + $holiday->oct + $holiday->nov + $holiday->dec;
        // dd($sum);
    }

    public static function convertAbbreviatedMonth($abbreviatedMonth)
    {
        $months = [
            'jan' => 'January',
            'feb' => 'February',
            'mar' => 'March',
            'apr' => 'April',
            'may' => 'May',
            'jun' => 'June',
            'jul' => 'July',
            'aug' => 'August',
            'sep' => 'September',
            'oct' => 'October',
            'nov' => 'November',
            'dec' => 'December',
        ];

        $abbreviatedMonth = strtolower($abbreviatedMonth);
        return $months[$abbreviatedMonth] ?? false;
    }

}
