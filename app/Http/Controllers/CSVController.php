<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Helpers\CSVParsingHelper as CSVParsing;

class CSVController extends Controller
{
    /**
     * Parse the uploaded CSV file
     */
    public function parseCSV(Request $request)
    {                
        // Check if we have a CSV file within our form submission
        if ($request->hasFile('csv')) {
            // Save a timestamp of the current time to avoid corrupt
            $timestamp = time();

            // Declare the CSV variable
            $csv = $request->file('csv');
            
            // Explode the filename to create an array to save the upload
            $filename_explode = explode('.', $csv->getClientOriginalName());

            // Retrieve the original filename to use to store the CSV file
            $filename = "{$filename_explode[0]}-{$timestamp}.{$filename_explode[1]}";

            // Store the file within the uploads
            $csv->move(storage_path() . config('app.uploaded_files_directory'), $filename);

            // Get the path of the CSV file
            $csv_path = storage_path() . config('app.uploaded_files_directory') . '/' . $filename;
            
            // Create an array of parsed CSV data
            $parsed_csv = array_map('str_getcsv', file($csv_path));

            // Loop through the first iteration of the CSV
            foreach ($parsed_csv[0] as $parsed_header_index => $parsed_header) {
                // Clean the header as special characters are added due to an encoding error
                $parsed_csv[0][$parsed_header_index] = preg_replace('/[^A-Za-z0-9. -]/', '', $parsed_header);
            }

            // Map the header names to the array
            array_walk($parsed_csv, function(&$a) use ($parsed_csv){
                $a = array_combine($parsed_csv[0], $a);
            });

            // Then remove the headers
            array_shift($parsed_csv);


            // Loop through each CSV item
            // This loop is under the assumption that the CSV file will always be the proper format
            foreach ($parsed_csv as $csv_row) {
                // Create the new CSVParsing helper object
                $csv_parser = new CSVParsing;

                // Create the new Transaction object for injection
                $transaction = new Transaction;

                // Format the date to be the correct MySQL format
                $dateTime = DateTime::createFromFormat('Y-m-d h:iA', $csv_row['Date']);

                // Assign the objects to their correct DB field
                $transaction->date = $dateTime->format('Y-m-d H:i:s');
                $transaction->transaction_code = $csv_row['TransactionNumber'];
                $transaction->transaction_valid = $csv_parser->verifyKey($csv_row['TransactionNumber']);
                $transaction->customer_number = (int) $csv_row['CustomerNumber'];
                $transaction->reference = $csv_row['Reference'];
                $transaction->amount = (float) $csv_row['Amount'];
                
                // Save the transaction
                // and if so, redirect the user
                $transaction->save();
            }

            // Redirect the user afterwards
            return redirect()->route('home');
        } else {
            return redirect()->route('home');
        }
    }

    /**
     * Display all transactions logs from the bank.
     * 
     * @return view
     */
    public function view()
    {
        // Create a new finalised empty transaction array that we will pass through to the front end
        $transactions_finalised = [];

        // Retrieve all the transaction rows
        $transactions = Transaction::orderBy('date')->get();

        // Check if we actually have transactions
        if ($transactions) {
            // Loop through all transactions
            foreach ($transactions as $transaction) {
                // Update the date field to be formatted
                $date = \Carbon\Carbon::parse($transaction->date)->format('d/m/Y h:iA');
    
                // Determine the correct term if the transaction is valid
                $transaction_valid = ($transaction->transaction_valid ? 'Yes' : 'No');
    
                // Format the amount of the dollars
                $amount = number_format(($transaction->amount / 100), 2);
    
                // Get the amount type if the number is negative or not
                if ($amount < 0) {
                    $amount_type = 'credit';
                } else {
                    $amount_type = 'debit';
                }

                // Add a currency symbol to the amount
                if ($amount < 0) {
                    $amount = str_replace('-', '-$', $amount);
                } else {
                    $amount = '$' . $amount;
                }
    
                // Append to the array
                $transactions_finalised[] = [
                    'date' => $date,
                    'transaction_code' => $transaction->transaction_code,
                    'transaction_valid' => $transaction_valid,
                    'customer_number' => $transaction->customer_number,
                    'reference' => $transaction->reference,
                    'amount' => $amount,
                    'amount_type' => $amount_type
                ];
            }
        }

        // Return the view containing all the transactions
        return view('home', ['transactions' => $transactions_finalised]);
    }
}
