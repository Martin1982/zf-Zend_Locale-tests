<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $locale = new Zend_Locale();
        $this->view->locale = (string) $locale;

        $locale = new Zend_Locale(Zend_Locale::BROWSER);
        $this->view->browserLocale = (string) $locale;

        try {
            $locale = new Zend_Locale(Zend_Locale::ENVIRONMENT);
        } catch(Exception $e) {
            $this->view->noEnv = true;
            $locale = new Zend_Locale('de_DE');
        }
        $this->view->envLocale = (string) $locale;

        Zend_Locale::setDefault('en_US');
        $locale = new Zend_Locale(Zend_Locale::ZFDEFAULT);
        $this->view->setLocale = (string) $locale;
    }

    public function measureAction()
    {
        // give NL weight
        $locale = new Zend_Locale('nl_NL');
        $unitType = Zend_Measure_Weight::GRAM;

        $weight = 200.05;

        $measure = new Zend_Measure_Weight($weight, $unitType ,$locale);
        $this->view->weightNL = $measure->toString();

        // give US weight
        $locale = new Zend_Locale('en_US');
        $unitType = Zend_Measure_Weight::LBS;

        $measure->convertTo($unitType);
        $weight = $measure->getValue();

        $measure = new Zend_Measure_Weight($weight, $unitType, $locale);
        $this->view->weightUS = $measure->toString();
    }

    public function basicAction()
    {
        // Extract values from a locale
        $locale = new Zend_Locale('fr_FR');
        $this->view->language = $locale->getLanguage();
        $this->view->region = $locale->getRegion();

        // Extract human readable values from a locale
        $this->view->fullLang = 
            Zend_Locale::getTranslation('fr', 'Language', 'en');
        $this->view->fullRegion =
            Zend_Locale::getTranslation('FR', 'Territory', 'en');
    }

    public function listAction()
    {
        // Gives an array of months in various
        // formats
        $this->view->months =
            Zend_Locale::getTranslationList('Months', 'nl_NL');
    }

    public function dateAction()
    {
        $this->view->dateUS = new Zend_Date();
        $this->view->dateNL = new Zend_Date('nl_NL');
    }

    public function timeAction()
    {
        $time = new Zend_TimeSync('pool.ntp.org');
        // returns a Zend_Date object
        $this->view->time = (string) $time->getDate('fr_FR');
    }

    public function cashAction()
    {
        $currencyNL = new Zend_Currency(null, 'nl_NL');
        $currencyUK = new Zend_Currency(null, 'en_GB');

        $this->view->nameNL = $currencyNL->getName();
        $this->view->nameUK = $currencyUK->getName();

        $this->view->signNL = $currencyNL->getSymbol();
        $this->view->signUK = $currencyUK->getSymbol();
    }

    public function cashoptAction()
    {
        $currency = new Zend_Currency(
            array(
                'display'   => Zend_Currency::USE_NAME,
                'locale'    => 'en_US',
                'value'     => 10
            )
        );
        $this->view->payMe = (string) $currency;
    }

    public function postcodeAction()
    {
        $countrycode = $this->_request->getParam('countrycode', '');
        $locale = $this->_request->getParam('locale', '');
        if (
            !empty($countrycode) &&
            !empty($locale) &&
            Zend_Locale::isLocale($locale)
        ) {
            $validate = new Zend_Validate_Int($locale);
            if (!$validate->isValid($countrycode)) {
                throw new Exception('Country code liar detected!');
            } else {
                exit('All your country code are belong to us');
            }
        } elseif (!empty($locale) && !Zend_Locale::isLocale($locale)) {
            throw new Exception ('Locale liar detected!');
        }
    }

    public function questionableAction()
    {
        try {
            $localeString = Zend_Locale::findLocale('es_ES');
        } catch (Exception $e) {
            trigger_error($e->getMessage());
        }
        $locale = new Zend_Locale($localeString);
        $simpleQuestion = $locale->getQuestion();
        echo "<pre>";
        var_dump($simpleQuestion);
        echo "</pre>";
        die();

    }

    public function translatedAction()
    {
        // Do not warn for missing translations in production
        $disableNotices = APPLICATION_ENV === 'production' ?
            true :  false;

        // create the translator
        $translator = new Zend_Translate(
            array(
                'locale' => 'fr',
                'adapter' => 'Array',
                'disableNotices' => $disableNotices,
                'content'   => APPLICATION_PATH.'/langs/',
                'scan' => Zend_Translate::LOCALE_FILENAME
            )
        );

        Zend_Registry::set('Zend_Translate', $translator);
    }


}

