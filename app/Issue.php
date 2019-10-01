<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Issue extends Model implements Searchable
{
    protected $fillable = [
        'description', 'priority', 'user_id', 'project_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User')->withTimestamps();
    }

    public function notes()
    {
        return $this->hasMany('App\IssueNote');
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function getSearchResult(): SearchResult
    {
        return new SearchResult(
            $this,
            $this->description,
            '/issue/' . $this->id
        );
    }

    public function uploadFile($fileName, $maxSize, $allowedExtensions)
    {
        //uploadFile() uploads a file to folder Uploads/Issue
        //PARAMs 
            //$fileName name of file declared in the html form i.e. <input type="file" name="fileName"> 
            //$maxSize maximum size of uploaded file in kb's 
            //$allowedExtensions array of allowed file types
        /*
        Example of $allowedExtensions format
        array(
                  'pdf' => 'application/pdf',
                  'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                  'txt' => 'text/plain',
                  'doc' => 'application/msword',
                  'latex' => 'application/x-latex',
                  'tex' => 'application/x-latex',
                  'odt' => 'application/vnd.oasis.opendocument.text', 
                  'rtf' => 'application/rtf',
                  'wks' => 'application/vnd.ms-works',
                  'wps' => 'application/vnd.ms-works',
                  'wpd' => 'application/vnd.wordperfect',
                  'jpg' => 'image/jpeg',
                  'png' => 'image/png',
              )
        */
        //$_FILES is a super global 

        //Get the name of the file
        $name_uploadfile = basename($_FILES[$fileName]['name']);

        //Get the size of the file
        $size_uploadfile = $_FILES[$fileName]['size']/1024; //Size in kb's

        
        
        //Validation
        try {
          if ( !isset($_FILES[$fileName]['error']) || is_array($_FILES[$fileName]['error'])) 
          {
            throw new RuntimeException("Ógildar breytur í viðhengi $name_uploadfile "); 
          }

          // Check $_FILES[$fileName]['error'] for any errors in the uploaded file
          switch ($_FILES[$fileName]['error']) {
            case UPLOAD_ERR_OK:
              break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
              throw new RuntimeException("Attachment $name_uploadfile is too big");
            default:
              throw new RuntimeException("Unknown error in attachment $name_uploadfile ");
            }

          // Check if the size is bigger then the maxSize
          if($size_uploadfile > $maxSize) {
            throw new RuntimeException("Attachment $name_uploadfile is too big, maximum size is $maxSize KB");
          }


          // Check "Mime" type of attachment manually for security
          $finfo = new finfo(FILEINFO_MIME_TYPE);
          if (false === $ext = array_search(
              $finfo->file($_FILES[$fileName]['tmp_name']),
              $allowedExtensions,
              true
          ))  {
                throw new RuntimeException("Attachment type in $name_uploadfile not supported");
              }


          //We create a new name for the attachment using hash for security and to avoid clashing names
          $destination = sprintf("./Uploads/Issue/%s.%s", sha1_file($_FILES[$fileName]["tmp_name"]), $ext);

          //We try to upload the file
          if (move_uploaded_file($_FILES[$fileName]['tmp_name'], $destination))
          {
            echo "Upload successful\n";
          } else {
            throw new RuntimeException("Upload of attachment $name_uploadfile unsuccessful");
          }
        } catch (RuntimeException $e) {
          echo $e->getMessage();
        }
    }
}
