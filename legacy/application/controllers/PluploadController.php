<?php

declare(strict_types=1);

class PluploadController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('upload', 'json')
            ->addActionContext('recent-uploads', 'json')
            ->initContext();
    }

    public function indexAction()
    {
        $locale = Application_Model_Preference::GetLocale();

        $this->view->headScript()->appendFile(Assets::url('js/datatables/js/jquery.dataTables.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/plupload/plupload.full.min.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/plupload/jquery.plupload.queue.min.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/library/plupload.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/plupload/i18n/' . $locale . '.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/libs/dropzone.min.js'), 'text/javascript');

        $this->view->headLink()->appendStylesheet(Assets::url('css/plupload.queue.css'));
        $this->view->headLink()->appendStylesheet(Assets::url('css/addmedia.css'));
        $this->view->headLink()->appendStylesheet(Assets::url('css/dashboard.css'));

        $this->view->quotaLimitReached = false;
        if (Application_Model_Systemstatus::isDiskOverQuota()) {
            $this->view->quotaLimitReached = true;
        }

        // Because uploads are done via AJAX (and we're not using Zend form for those), we manually add the CSRF
        // token in here.
        $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
        // The CSRF token is generated in Bootstrap.php

        $csrf_element = new Zend_Form_Element_Hidden('csrf');
        $csrf_element->setValue($csrf_namespace->authtoken)->setRequired('true')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $csrf_form = new Zend_Form();
        $csrf_form->addElement($csrf_element);
        $this->view->form = $csrf_form;

        // get max upload files size in MiB for plupload js.
        $uploadMaxSize = $this->file_upload_max_size() / 1024 / 1024;
        if ($uploadMaxSize === 0) {
            // fall back to old default behaviour if unlimited uploads are
            // configured on the server side.
            $uploadMaxSize = 500;
        }
        $this->view->uploadMaxSize = $uploadMaxSize;
    }

    public function uploadAction()
    {
        $current_namespace = new Zend_Session_Namespace('csrf_namespace');
        $observed_csrf_token = $this->_getParam('csrf_token');
        $expected_csrf_token = $current_namespace->authtoken;

        if ($observed_csrf_token == $expected_csrf_token) {
            $upload_dir = ini_get('upload_tmp_dir') . DIRECTORY_SEPARATOR . 'plupload';
            $tempFilePath = Application_Model_StoredFile::uploadFile($upload_dir);
            $tempFileName = basename($tempFilePath);

            $this->_helper->json->sendJson(['jsonrpc' => '2.0', 'tempfilepath' => $tempFileName]);
        } else {
            $this->_helper->json->sendJson(['jsonrpc' => '2.0', 'valid' => false, 'error' => 'CSRF token did not match.']);
        }
    }

    public function recentUploadsAction()
    {
        $request = $this->getRequest();

        $filter = $request->getParam('uploadFilter', 'all');
        $limit = intval($request->getParam('iDisplayLength', 10));
        $rowStart = intval($request->getParam('iDisplayStart', 0));

        $recentUploadsQuery = CcFilesQuery::create();
        // old propel 1.5 to reuse this query item (for counts/finds)
        $recentUploadsQuery->keepQuery(true);

        // Hide deleted files
        $recentUploadsQuery->filterByDbFileExists(true);

        $numTotalRecentUploads = $recentUploadsQuery->count();
        $numTotalDisplayUploads = $numTotalRecentUploads;

        if ($filter == 'pending') {
            $recentUploadsQuery->filterByDbImportStatus(1);
            $numTotalDisplayUploads = $recentUploadsQuery->count();
        } elseif ($filter == 'failed') {
            $recentUploadsQuery->filterByDbImportStatus(2);
            $numTotalDisplayUploads = $recentUploadsQuery->count();
            // TODO: Consider using array('min' => 200)) or something if we have multiple errors codes for failure.
        }

        $recentUploads = $recentUploadsQuery
            ->orderByDbUtime(Criteria::DESC)
            ->offset($rowStart)
            ->limit($limit)
            ->find();

        $uploadsArray = [];
        $utcTimezone = new DateTimeZone('UTC');
        $displayTimezone = new DateTimeZone(Application_Model_Preference::GetUserTimezone());

        foreach ($recentUploads as $upload) {
            $upload = $upload->toArray(BasePeer::TYPE_FIELDNAME);
            // TODO: $this->sanitizeResponse($upload));
            $upload['utime'] = new DateTime($upload['utime'], $utcTimezone);
            $upload['utime']->setTimeZone($displayTimezone);
            $upload['utime'] = $upload['utime']->format(DEFAULT_TIMESTAMP_FORMAT);

            // TODO: Invoke sanitization here (MediaController's removeBlacklist stuff)
            array_push($uploadsArray, $upload);
        }

        $this->view->sEcho = intval($request->getParam('sEcho'));
        $this->view->iTotalDisplayRecords = $numTotalDisplayUploads;
        $this->view->iTotalRecords = $numTotalRecentUploads;
        $this->view->files = SecurityHelper::htmlescape_recursive($uploadsArray);
    }

    /**
     * get configured upload max size from php.
     *
     * Pinched from Drupal: https://github.com/drupal/drupal/blob/4204b0b29a7318008f10765cf88114bf3ed21c32/core/includes/file.inc#L1099
     *
     * Drupal seems to be the only framework that does this somewhat the right
     * way. I'm adding the method here since it's part of their core and I did
     * not find an easy way to grab that thrrough composer in an isolated way.
     */
    private function file_upload_max_size()
    {
        static $max_size = -1;
        if ($max_size < 0) {
            // Start with post_max_size.
            $max_size = $this->bytes_to_int(ini_get('post_max_size'));

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = $this->bytes_to_int(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }

        return $max_size;
    }

    /**
     * Pinched from Drupal: https://github.com/drupal/drupal/blob/4204b0b29a7318008f10765cf88114bf3ed21c32/core/lib/Drupal/Component/Utility/Bytes.php#L27.
     *
     * This is the real point of importing the Drupal solution. They have done
     * an implementation for figuring out what the user specified in the
     * post_max_size and upload_max_size configuration. Sadly php does not
     * support a nice way to get at the results of this config after it is
     * parsed by the engine, hence the below hack.
     *
     * @param mixed $size
     */
    private function bytes_to_int($size)
    {
        // Remove the non-unit characters from the size.
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        // Remove the non-numeric characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size);
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power
            // of magnitude to multiply a kilobyte by.
            return round($size * 1024 ** stripos('bkmgtpezy', $unit[0]));
        }

        return round($size);
    }
}
