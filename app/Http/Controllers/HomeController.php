<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * conutry to select from
     */
    private function selectContries(){
        return [
            'Pakistan',
        ];
    }
    
    /**
     * csv file names
     */
    private function dataFiles(){
        return [
            '2021-01-22',
            '2021-02-22',
            '2021-03-22',
        ];
    }

    /**
     * process data in requires format/order
     * 'bycountry' => [
     *   0 => [ reord from file 1 ]
     *   1 => [ reord from file 2 ]
     *   2 => [ reord from file 3 ]
     * ]
     */
    private function processData($data){
        $pdata = [];
        $records = count($data);
        foreach($data[0] as $ck => $counrty){
            foreach($counrty as $key => $row){
                for ($i = 0; $i < $records; $i++) { 
                    if($i > 0){
                        $data[$i][$ck][$key]['increaseInCases'] = $data[$i][$ck][$key]['Confirmed'] - $data[$i-1][$ck][$key]['Confirmed'];
                    }
                    $pdata[$ck][] = $data[$i][$ck][$key];
                }
            }
        }
        return $pdata;
    }

    /**
     * function for to get data from CSV
     */
    private function csvToArray($filename = '', $delimiter = ','){
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }
        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $rw = array_combine($header, $row);
                    if(in_array($rw['Country_Region'], $this->selectContries())){
                        $temp = array_combine($header, $row);
                        $temp['increaseInCases'] = 0;
                        $data[$rw['Country_Region']][] = $temp;
                        $temp = [];
                    }
                }
            }
            fclose($handle);
        }
        return $data;
    }

    /**
     * function to retrive data 
     */
    public function showData(){
        $data = [];
        foreach($this->dataFiles() as $key => $name){
            $file = storage_path('data/'.$name.'.csv');
            $data[$key] = $this->csvToArray($file);
        }
        $data = $this->processData($data);
        return view('covid.data', compact('data'));
    }
    
    /**
     * email via sendgrid
     */
    public function sendMail(){

        $data = [];
        foreach ($this->dataFiles() as $key => $name) {
            $file = storage_path('data/' . $name . '.csv');
            $data[$key] = $this->csvToArray($file);
        }
        $data = $this->processData($data);

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("fiverr.com.me@gmail.com", "Muhammad Hamza");
        $email->setSubject("Covid | Records | Sending with Twilio SendGrid");
        $email->addTo("fiverr.com.me@gmail.com", "Muhammad Hamza");
        $email->addTo("faraz@sitealive.com", "Faraz");
        $email->addTo("sikander@sitealive.com", "Sikander");
        $email->addContent(
            "text/html", view('covid.data', compact('data'))->render()
        );
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }


    }
    
}
