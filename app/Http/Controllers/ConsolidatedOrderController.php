<?php

namespace App\Http\Controllers;

use App\Exports\ConsolidatedOrdersExport;
use App\Imports\ConsolidatedOrdersImport;
use App\Services\ConsolidatedOrderService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @OA\Info(
 *     title="E-commerce Database Optimization API",
 *     version="1.0.0",
 *     description="API for managing consolidated e-commerce orders with optimized analytics performance",
 *     @OA\Contact(
 *         email="lawalyusuf@example.com",
 *         name="Lawal Owolabi Yusuf"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://127.0.0.1:8000/api",
 *     description="Local development server"
 * )
 */

class ConsolidatedOrderController extends Controller
{
    protected ConsolidatedOrderService $service;

    public function __construct(ConsolidatedOrderService $service)
    {
        $this->service = $service;
    }

    /**
     * Display analytics dashboard
     * 
     * @OA\Get(
     *     path="/consolidated-orders",
     *     summary="Get analytics data",
     *     description="Retrieve comprehensive analytics data from consolidated orders with optional filtering",
     *     tags={"Analytics"},
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Start date filter (Y-m-d format)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="End date filter (Y-m-d format)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-12-31")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Order status filter",
     *         required=false,
     *         @OA\Schema(type="string", example="delivered")
     *     ),
     *     @OA\Parameter(
     *         name="customer_id",
     *         in="query",
     *         description="Customer ID filter",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Analytics data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="total_revenue", type="number", format="float", example=1807.45),
     *                 @OA\Property(property="total_orders", type="integer", example=3),
     *                 @OA\Property(property="total_items", type="integer", example=6),
     *                 @OA\Property(property="avg_order_value", type="number", format="float", example=602.48),
     *                 @OA\Property(property="top_products", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="revenue_by_month", type="array", @OA\Items(type="object"))
     *             ),
     *             @OA\Property(property="filters", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'status', 'customer_id']);
        $analytics = $this->service->getAnalyticsData($filters);

        return response()->json([
            'success' => true,
            'data' => $analytics,
            'filters' => $filters
        ]);
    }

    /**
     * Populate consolidated orders table
     * 
     * @OA\Post(
     *     path="/consolidated-orders/populate",
     *     summary="Populate consolidated orders",
     *     description="Trigger the population/refresh of consolidated orders table with optimized batch processing",
     *     tags={"Data Management"},
     *     @OA\Response(
     *         response=200,
     *         description="Data populated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully processed 345 records"),
     *             @OA\Property(property="records_processed", type="integer", example=345)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error during population",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error populating consolidated orders: Database connection failed")
     *         )
     *     )
     * )
     */
    public function populate()
    {
        try {
            $recordsProcessed = $this->service->populateConsolidatedOrders();

            return response()->json([
                'success' => true,
                'message' => "Successfully processed {$recordsProcessed} records",
                'records_processed' => $recordsProcessed
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error populating consolidated orders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export consolidated orders to Excel
     * 
     * @OA\Get(
     *     path="/consolidated-orders/export",
     *     summary="Export to Excel",
     *     description="Download consolidated orders data as Excel file with optional filtering",
     *     tags={"Export/Import"},
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Start date filter (Y-m-d format)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="End date filter (Y-m-d format)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-12-31")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Order status filter",
     *         required=false,
     *         @OA\Schema(type="string", example="delivered")
     *     ),
     *     @OA\Parameter(
     *         name="customer_id",
     *         in="query",
     *         description="Customer ID filter",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Excel file download",
     *         @OA\MediaType(
     *             mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Export error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error exporting data: File generation failed")
     *         )
     *     )
     * )
     */
    public function export(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'status', 'customer_id']);
        $filename = 'consolidated_orders_' . date('Y-m-d_H-i-s') . '.xlsx';
        $filePath = 'exports/' . $filename;

        try {
            // Store the file first
            Excel::store(new ConsolidatedOrdersExport($filters), $filePath);

            // Get the full file path
            $fullPath = storage_path('app/' . $filePath);

            // Check if file exists
            if (!file_exists($fullPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate export file'
                ], 500);
            }

            // Return file as download response with proper headers
            return response()->download($fullPath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import consolidated orders from Excel
     * 
     * @OA\Post(
     *     path="/consolidated-orders/import",
     *     summary="Import from Excel",
     *     description="Upload and import consolidated orders data from Excel file with validation",
     *     tags={"Export/Import"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="Excel file (.xlsx, .xls, .csv)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Import completed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Import completed successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="file",
     *                     type="array",
     *                     @OA\Items(type="string", example="The file field is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Import error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error importing data: File format not supported")
     *         )
     *     )
     * )
     */
    public function import(Request $request)
    {
        // Ensure we're expecting JSON response
        $request->headers->set('Accept', 'application/json');

        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv'
            ]);

            Excel::import(new ConsolidatedOrdersImport, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Import completed successfully'
            ]);
        } catch (\Exception $e) {
            // Handle validation errors and other exceptions
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error importing data: ' . $e->getMessage()
            ], 500);
        }
    }
}
