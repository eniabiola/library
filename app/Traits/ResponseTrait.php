<?php

namespace App\Traits;

trait ResponseTrait
{
    public function successResponse($message, $status=200, $data = "")
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public function failedResponse($message, $status=400, $data = "")
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public function paginatingResponse($query, $page, $limit)
    {
        $start = ((($page*$limit) - $limit) + 1);
        $count = $query->count();

        $query = $query
            ->offset($start)
            ->limit($limit)
            ->get();

        $result = array();

        array_push($result, $query);
        $result[] = ['total_number' => $count];
        return $result;
    }
}
