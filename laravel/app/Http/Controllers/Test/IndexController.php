<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Validator;
use Response;


class IndexController extends Controller
{
    private $bankNoteTwenty = 20;
    private $bankNoteFifty = 50;
    private $bankNoteOneHundred = 100;
    private $bankNoteFiveHundred = 500;
    private $bankNoteOneThousand = 1000;

    public function index() {
        return view('atm');
    }

    public function postWithdrawMoney(Request $request) {
        $validator = Validator::make($request->all(), [
            'withdraw' => 'required|integer'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $e = '';
            foreach ($errors->all() as $message) {
                $e .= $message;
            }

            return Response()->json([
                'status'  => false,
                'message' => $e
            ]);
        }

        $withdrawMoney = $request->input('withdraw'); // Withdraw
        $remainMoney = 10000000;

        // Banknote in machine
        $remainBankNote = [
            '20'    => 100,
            '50'    => 100,
            '100'   => 100,
            '500'   => 100,
            '1000'  => 100,
        ];
        krsort($remainBankNote);

        $totalBankNoteTwenty = $this->bankNoteTwenty * $remainBankNote['20'];
        $totalBankNoteFifty = $this->bankNoteFifty * $remainBankNote['50'];
        $totalBankNoteOneHundred = $this->bankNoteOneHundred * $remainBankNote['100'];
        $totalBankNoteFiveHundred = $this->bankNoteFiveHundred * $remainBankNote['500'];
        $totalBankNoteOneThousand = $this->bankNoteOneThousand * $remainBankNote['1000'];

        $moneyATMTotal = $totalBankNoteTwenty + $totalBankNoteFifty + $totalBankNoteOneHundred + $totalBankNoteFiveHundred + $totalBankNoteOneThousand;

        $checkWithdraw = 0;
        $tempWithdrawMoney = $withdrawMoney;
        foreach ($remainBankNote as $key => $value) {
            if ($value > 0) {
                $checkWithdraw = floor($tempWithdrawMoney / $key);
                $tempWithdrawMoney =  $tempWithdrawMoney - ($checkWithdraw * $key);
            }
        }

        if($tempWithdrawMoney > 0) {
            $remainBankNote = [
                '20'    => 100,
                '50'    => 100,
                '100'   => 100,
                '500'   => 100,
                '1000'  => 100,
            ];
            $tempWithdrawMoney = $withdrawMoney;
            foreach ($remainBankNote as $key => $value) {
                if ($value > 0) {
                    $checkWithdraw = floor($tempWithdrawMoney / $key);
                    $tempWithdrawMoney =  $tempWithdrawMoney - ($checkWithdraw * $key);
                }
            }
        }

        // Check withdraw below bank note 20 
        if ($tempWithdrawMoney > 0) {
            return Response()->json([
                'status'  => false,
                'message' => 'Can\'t windraw because number not integer'
            ]);
        }

        if ($remainMoney < $withdrawMoney || $moneyATMTotal < $withdrawMoney) {
            return Response()->json([
                'status'  => false,
                'message' => 'not enough money'
            ]);
        }

        $calculateBankNote = $this->calculateBankNoteBasic($remainBankNote, $withdrawMoney);
        $calculateBankNoteTwo = $this->calculateBankNoteOption($remainBankNote, $withdrawMoney);

        $response = [
            'basic'     =>  $calculateBankNote,
            'option'    =>  ($calculateBankNoteTwo !== false) ? $calculateBankNoteTwo : false
        ];
        
        return Response()->json([
            'status'   => true,
            'response' => $response
        ]);
    }

    private function calculateBankNoteBasic($remainBankNote, $withdrawMoney) {
        $receivedBankNote = [];
        $receivedBankNoteTemp = $remainBankNote;
        $receivedBankNoteTemp = array_keys($receivedBankNoteTemp);
        $tempMoneyA = $withdrawMoney;
        $tempMoneyB = 0;
        
        foreach ($receivedBankNoteTemp as $key => $remianNote) {
            if($tempMoneyA >= $receivedBankNoteTemp[$key]) {
                if($key > 0) {
                    if($withdrawMoney >= $receivedBankNoteTemp[$key] && $withdrawMoney < $receivedBankNoteTemp[$key - 1]) {
                        $tempMoneyB = $withdrawMoney;
                    }

                    $totalBankNote = (int)($tempMoneyB / $receivedBankNoteTemp[$key]);
                } else {
                    $totalBankNote = (int)($tempMoneyA / $receivedBankNoteTemp[$key]);
                }

                if($totalBankNote > $remainBankNote[$receivedBankNoteTemp[$key]]) {
                    $receivedBankNote[$receivedBankNoteTemp[$key]] = $remainBankNote[$receivedBankNoteTemp[$key]];
                } else {
                    $receivedBankNote[$receivedBankNoteTemp[$key]] = $totalBankNote;
                }
                
                $temp = $tempMoneyB;
                if ($totalBankNote == $receivedBankNote[$receivedBankNoteTemp[$key]] || $receivedBankNote[$receivedBankNoteTemp[$key]] > $totalBankNote) {
                    if ($key == 0) {
                        $tempMoneyB = ($tempMoneyA - ($receivedBankNote[$receivedBankNoteTemp[$key]] * $receivedBankNoteTemp[$key]));
                    } else {
                        $tempMoneyB = ($temp - ($receivedBankNote[$receivedBankNoteTemp[$key]] * $receivedBankNoteTemp[$key]));
                    }
                } else if ($remainBankNote[$remianNote] < $totalBankNote) {
                    if ($key == 0) {
                        $tempMoneyB = ($tempMoneyA - ($remainBankNote[$remianNote] * $receivedBankNoteTemp[$key]));
                    } else {
                        $tempMoneyB = ($temp - ($remainBankNote[$remianNote]* $receivedBankNoteTemp[$key]));
                    }
                } else {
                    $receivedBankNote[$receivedBankNoteTemp[$key]] = 0;
                    $tempMoneyB = $tempMoneyA;
                }
            }
        }

        if ($tempMoneyB > 0) {
            return false;
        } else {
            return $receivedBankNote;
        }
    }

    private function calculateBankNoteOption($remainBankNote, $withdrawMoney) {
        foreach($remainBankNote as $bankNote => $remainNote) {
            $tempNote = $remainBankNote;
            $tempNote[$bankNote] = 0;
            $probability = $this->calculateBankNoteBasic($tempNote, $withdrawMoney);
            if ($probability != false) {
                $setBankNote[] = $probability;
            }
        }

        if (count($setBankNote) > 0) {
            $data = array_unique($setBankNote, SORT_REGULAR);
            foreach($data as $val) {
                $setReturn[] = $val;
            }

            return $setReturn;
        }

        return false;
    }
}