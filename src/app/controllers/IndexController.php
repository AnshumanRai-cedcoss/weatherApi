<?php

use Phalcon\Mvc\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;


/**
 * Index class
 * The first class to be loaded
 */
class IndexController extends Controller
{
    /**
     * index function
     * The searched form is displayed here
     * @return void
     */
    public function indexAction()
    {

    }
    public function sportsAction()
    {
        $url = $this->request->get('url');   
        $client = new Client([
           'base_uri' => 'http://api.weatherapi.com'
       ]);
       $key = "0bab7dd1bacc418689b143833220304";
       $response = $client->request('GET', "/v1/sports.json?key=$key&q=$url");

       echo "<pre>";
       $r = json_decode($response->getBody(),true);
     $this->view->data = $r;
//  print_r($r);
//  die;//      
    }

    public function alertAction()
    {
        $url = $this->request->get('url');   
        $client = new Client([
           'base_uri' => 'http://api.weatherapi.com'
       ]);
       $key = "0bab7dd1bacc418689b143833220304";
       $response = $client->request('GET', "/v1/forecast.json?key=$key&q=$url&alerts=yes");

       echo "<pre>";
       $r = json_decode($response->getBody(),true);
       $this->view->data = $r["alerts"]["alert"];
  
    //    print_r($r["alerts"]["alert"]);
    //    die;
    }

    public function historyAction()
    {
        $url = $this->request->get('url');   
        $client = new Client([
           'base_uri' => 'http://api.weatherapi.com'
       ]);
       $date = date("Y-m-d",strtotime("-1 days"));
       $dateE = date("Y-m-d",strtotime("-4 days"));

       $key = "0bab7dd1bacc418689b143833220304";
       $response = $client->request('GET', "/v1/history.json?key=$key&q=$url&dt=$dateE&end_dt=$date");

       echo "<pre>";
       $r = json_decode($response->getBody(),true);
       $r= $r["forecast"]["forecastday"];
     
       $data = array();
       foreach ($r as $key => $value) {
           $one  = array("date"=> $value["date"],"day"=> $value["day"]);

           array_push($data,$one);
       }
       $this->view->data = $data;
    //    print_r($data);
    //    die;
    }


    public function astronomyAction()
    {
        $url = $this->request->get('url');   
        $client = new Client([
           'base_uri' => 'http://api.weatherapi.com'
       ]);
       $key = "0bab7dd1bacc418689b143833220304";
       $response = $client->request('GET', "/v1/astronomy.json?key=$key&q=$url&aqi=no");

       echo "<pre>";
       $r = json_decode($response->getBody(),true);
       $this->view->data = $r;
    //    print_r(($r));
    //    die;
    }

    public function timezoneAction()
    {
        $url = $this->request->get('url');   
        $client = new Client([
           'base_uri' => 'http://api.weatherapi.com'
       ]);
       $key = "0bab7dd1bacc418689b143833220304";
       $response = $client->request('GET', "/v1/timezone.json?key=$key&q=$url&aqi=no");

       echo "<pre>";
       $r = json_decode($response->getBody(),true);
       $this->view->data = $r;
   
    }

    public function forecastAction()
    {
        $url = $this->request->get('url');   
         $client = new Client([
            'base_uri' => 'http://api.weatherapi.com'
        ]);
        $key = "0bab7dd1bacc418689b143833220304";
        $response = $client->request('GET', "/v1/forecast.json?key=$key&q=$url&aqi=no");

        echo "<pre>";
        $r = json_decode($response->getBody(),true);
        $this->view->data = $r;
        $this->view->hour = (int)date("H",strtotime($r["location"]["localtime"]));
  
    // print_r($r["forecast"]["forecastday"][0]["hour"]);
    //   die;
    }

    public function airQualityAction()
    {
         $url = $this->request->get('url');   
         $client = new Client([
            'base_uri' => 'http://api.weatherapi.com'
        ]);
        $key = "0bab7dd1bacc418689b143833220304";
        $response = $client->request('GET', "/v1/current.json?key=$key&q=$url&aqi=yes");

        echo "<pre>";
      
        $this->view->data = json_decode($response->getBody());
        $r = json_decode($response->getBody(),true);
        $this->view->quality = $r["current"]["air_quality"];
     
        // print_r($r["current"]["air_quality"]);
        // die;
    }
    /**
     * ProductDetail function
     * Used to display the info of the particular book clicked
     * @return void
     */
    public function productDetailAction()
    {
        
            $url = $this->request->get("url");
  
            $client = new Client([
                'base_uri' => 'http://api.weatherapi.com'
            ]);
            $key = "0bab7dd1bacc418689b143833220304";
            $response = $client->request('GET', "/v1/current.json?key=$key&q=$url&aqi=no");

            echo "<pre>";
            print_r(json_decode($response->getBody()));
    
            $this->view->data = json_decode($response->getBody());

    }


    /**
     * DisplayAll function
     * Used to display the books according to the name searched
     * @return void
     */
    public function displayAllAction()
    {
        if (count($this->request->getPost()) > 0) {
        
                $name = $this->request->get("input1");
                $client = new Client([
                    'base_uri' => 'http://api.weatherapi.com'
                ]);
                $key = "0bab7dd1bacc418689b143833220304";
                $response = $client->request('GET', "/v1/search.json?key=$key&q=$name");
    
                echo "<pre>";
                print_r(json_decode($response->getBody()));
                $this->view->data = json_decode($response->getBody());
      
        
        }
    }

    public function errorAction()
    {
     

    }
}
