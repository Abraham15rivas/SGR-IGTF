<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

trait RouteFileTrait {
    
    public function routeForExcel($date_req) {
        $date_req   = Carbon::parse($date_req)->format('d-m-Y');
        $part_date  = explode('-', $date_req);
        $full_date  = str_replace('-', '', $date_req);
        $date       = Carbon::parse($date_req);
        $month      = ucfirst($date->translatedFormat("F"));
        $name       = 'IGTF_' . substr($date_req, 0, 6) . substr($date_req, 8, 10);
        $route      = "IGTF/REPORTE_EXCEL/AÑO$part_date[2]/$month$part_date[2]/$full_date/$name.xls";
        
        return [
            $route,
            $name
        ];
    }

    public function routeITFBancoDetalle($date) {
        $date       = Carbon::parse($date)->format('d-m-Y');
        $part_date  = explode('-', $date);
        $full_date  = str_replace('-', '', $date);
        $date_new   = Carbon::parse($date);
        $month      = ucfirst($date_new->translatedFormat("F"));
        $week       = $date_new->weekday();
        $name       = 'ITF_' . substr($date, 0, 6) . substr($date, 8, 10);
        $route      = "IGTF/AÑO$part_date[2]/XML/$month$part_date[2]/XML_DETALLADO_DIARIO/SEMANA$week/$full_date/$name.xml";
        
        return [
            $route,
            $name
        ];
    }

    public function routeITFBanco($date) {
        $date           = Carbon::parse($date)->format('d-m-Y');
        $part_date      = explode('-', $date);
        $full_date      = str_replace('-', '', $date);
        $date_new       = Carbon::parse($date);
        $month          = ucfirst($date_new->translatedFormat("F"));
        $week           = $date_new->weekday();
        $route_general  = "IGTF/AÑO$part_date[2]/XML/$month$part_date[2]/XML_RESUMEN_DIARIO/Semana_$week/$full_date";
        $name           = 'ITF_Banco';
        $route          = "$route_general/$name.xml";

        return [
            $route,
            $name
        ];
    }

    public function routeITFBancoConfirmacion($date) {
        $date           = Carbon::parse($date)->format('d-m-Y');
        $part_date      = explode('-', $date);
        $full_date      = str_replace('-', '', $date);
        $date_new       = Carbon::parse($date);
        $month          = ucfirst($date_new->translatedFormat("F"));
        $week           = $date_new->weekday();
        $route_general  = "IGTF/AÑO$part_date[2]/XML/$month$part_date[2]/XML_RESUMEN_DIARIO/Semana_$week/$full_date";
        $name           = 'ITFBancoConfirmacion';
        $route          = "$route_general/confirmacion/$name.xml";

        return [
            $route,
            $name
        ];
    }
}