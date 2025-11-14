<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\LeaveTypeModel;
use App\Models\LeaveRecordModel;
use App\Models\MonthlySummaryModel;
use App\Utils\Validator;

class LeaveController
{
    private LeaveTypeModel $leaveTypeModel;
    private LeaveRecordModel $leaveRecordModel;
    private MonthlySummaryModel $monthlySummaryModel;

    public function __construct(LeaveTypeModel $leaveTypeModel, LeaveRecordModel $leaveRecordModel, MonthlySummaryModel $monthlySummaryModel)
    {
        $this->leaveTypeModel = $leaveTypeModel;
        $this->leaveRecordModel = $leaveRecordModel;
        $this->monthlySummaryModel = $monthlySummaryModel;
    }

    public function getLeaveTypes(Request $request, Response $response): Response
    {
        $types = $this->leaveTypeModel->findAll();
        $response->getBody()->write(json_encode($types));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getMonthlyRecords(Request $request, Response $response, array $args): Response
    {
        // Role-based access control: Only 'admin' or 'encoder' can view others' records
        $token = $request->getAttribute('token');
        $targetUserId = Validator::sanitizeAndValidateInt($args['user_id'] ?? $token['uid']);
        $monthYear = Validator::sanitizeAndValidateString($args['month_year'] ?? date('Y-m'), FILTER_SANITIZE_STRING, ['required' => true, 'max_length' => 7]); // YYYY-MM

        if (is_null($targetUserId) || is_null($monthYear)) {
            return $this->errorResponse($response, 'Invalid user ID or month/year format.', 400);
        }

        if ($token['role'] === 'employee' && $targetUserId !== $token['uid']) {
            return $this->errorResponse($response, 'Access denied. You can only view your own records.', 403);
        }

        $records = $this->leaveRecordModel->findByUserAndMonth($targetUserId, $monthYear);
        $summary = $this->monthlySummaryModel->findByUserMonthYear($targetUserId, (int)date('m', strtotime($monthYear)), (int)date('Y', strtotime($monthYear)));

        $response->getBody()->write(json_encode([
            'records' => $records,
            'summary' => $summary ?: ['vacation_leave_balance' => 0, 'sick_leave_balance' => 0]
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function saveMonthlyRecords(Request $request, Response $response): Response
    {
        // Role-based access control: Only 'admin' or 'encoder' can save records
        $token = $request->getAttribute('token');
        if (!in_array($token['role'], ['admin', 'encoder'])) {
            return $this->errorResponse($response, 'Access denied. Only administrators and encoders can save records.', 403);
        }

        $data = $request->getParsedBody();
        $userId = $data['user_id'] ?? null;
        $records = $data['records'] ?? []; // Array of ['date' => 'YYYY-MM-DD', 'leave_type_id' => 1]
        $summary = $data['summary'] ?? []; // ['vl_balance' => 10.5, 'sl_balance' => 5.0, 'month' => 9, 'year' => 2024]

        // Basic input validation and sanitization
        $userId = Validator::sanitizeAndValidateInt($data['user_id'] ?? null, ['required' => true]);
        $records = $data['records'] ?? [];
        $summary = $data['summary'] ?? [];

        if (is_null($userId) || empty($records) || empty($summary)) {
            return $this->errorResponse($response, 'Invalid or incomplete data provided.', 400);
        }

        // Validate records structure
        foreach ($records as $record) {
            $date = Validator::validateDate($record['date'] ?? '');
            $leaveTypeId = Validator::sanitizeAndValidateInt($record['leave_type_id'] ?? null, ['required' => true]);

            if (is_null($date) || is_null($leaveTypeId)) {
                return $this->errorResponse($response, 'Invalid record format (date or leave_type_id).', 400);
            }
        }

        // Validate summary structure
        $month = Validator::sanitizeAndValidateInt($summary['month'] ?? null, ['min_range' => 1, 'max_range' => 12]);
        $year = Validator::sanitizeAndValidateInt($summary['year'] ?? null, ['min_range' => 2000, 'max_range' => 2100]);
        $vlBalance = Validator::sanitizeAndValidateFloat($summary['vl_balance'] ?? null);
        $slBalance = Validator::sanitizeAndValidateFloat($summary['sl_balance'] ?? null);

        if (is_null($month) || is_null($year) || is_null($vlBalance) || is_null($slBalance)) {
            return $this->errorResponse($response, 'Invalid summary data provided.', 400);
        }

        // Save records
        $recordSaved = $this->leaveRecordModel->saveRecords($userId, $records);

        // Save summary
        $summarySaved = $this->monthlySummaryModel->saveOrUpdate(
            (int)$userId,
            (int)$summary['month'],
            (int)$summary['year'],
            (float)$summary['vl_balance'],
            (float)$summary['sl_balance']
        );

        if ($recordSaved && $summarySaved) {
            $response->getBody()->write(json_encode(['message' => 'Leave records and summary saved successfully.']));
            return $response->withHeader('Content-Type', 'application/json');
        }

        return $this->errorResponse($response, 'Failed to save one or more records.', 500);
    }

    private function errorResponse(Response $response, string $message, int $status): Response
    {
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
