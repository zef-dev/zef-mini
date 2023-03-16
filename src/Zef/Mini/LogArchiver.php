<?php declare(strict_types=1);

namespace Zef\Mini;

use Psr\Log\LoggerInterface;

class LogArchiver
{	
    private $_path;
    
    /**
     * @var LoggerInterface
     */
    private $_logger;
    
    private $_prefix;
    private $_daysToArchive;
    private $_daysToDelete;
    
    public function __construct( $logger, $path, $prefix, $daysToArchive, $daysToDelete)
    {
        $this->_logger          =   $logger;
        $this->_path            =   $path;
        $this->_prefix          =   $prefix;
        $this->_daysToArchive   =   $daysToArchive;
        $this->_daysToDelete    =   $daysToDelete;
    }
    
    public function isLogEnabled()
    {
        return !empty( $this->_path);
    }
    
    public function cleanup()
    {
        $this->_logger->info( 'Performing cleanup ['.$this.']');
        $files  =   $this->_findFiles( 'gz');
        $files  =   $this->_getFilesOlderThan( $files, $this->_getFormattedDate( $this->_daysToDelete));
        
        $this->_logger->info( 'Got ['.count( $files).'] files to delete');
        
        foreach ( $files as $file_data)
        {
            $this->_logger->debug( 'Handling gz file ['.$file_data['filename'].']');
            unlink( $file_data['filename']);
        }
    }
    
    public function archive()
    {
        $this->_logger->info( 'Performing archive ['.$this.']');
        
        $files  =   $this->_findFiles( 'log');
        $files  =   $this->_getFilesOlderThan( $files, $this->_getFormattedDate( $this->_daysToArchive));
        
        $this->_logger->info( 'Got ['.count( $files).'] files to archive');
        
        foreach ( $files as $file_data) 
        {
            $this->_logger->debug( 'Handling log file ['.$file_data['filename'].']');
            $this->_gzcompressfile( $file_data['filename']);
            unlink( $file_data['filename']);
        }
    }
    
    private function _getFormattedDate( $daysBefore) {
        return date('Y-m-d', strtotime( $daysBefore.' days ago'));
    }
    
    private function _getFilesOlderThan( $files, $date) 
    {
        $this->_logger->debug( 'Filtering files older than ['.$date.']');
        
        $filtered = array_filter( $files, function( $a) use ( $date) {
            return $a['basename'] < $date;
        });
        
        return $filtered;
    }
    
    private function _findFiles( $ext) 
    {
        $all = [];
        $fileList = glob( $this->_path.'/*.'.$ext);
        foreach( $fileList as $filename)
        {
            if( is_file( $filename)) {
                $all[] = [
                    'filename' => $filename,
                    'basename' => str_replace( $this->_prefix, '', basename( $filename, '.'.$ext))
                ];
            }
        }
        
        return $all;
    }
    
    private function _gzcompressfile(string $inFilename, int $level = 9): string
    {
        // Is the file gzipped already?
        $extension = pathinfo($inFilename, PATHINFO_EXTENSION);
        if ($extension == "gz") {
            return $inFilename;
        }
        
        // Open input file
        $inFile = fopen($inFilename, "rb");
        if ($inFile === false) {
            throw new \Exception("Unable to open input file: $inFilename");
        }
        
        // Open output file
        $gzFilename = $inFilename.".gz";
        $mode = "wb".$level;
        $gzFile = gzopen($gzFilename, $mode);
        if ($gzFile === false) {
            fclose($inFile);
            throw new \Exception("Unable to open output file: $gzFilename");
        }
        
        // Stream copy
        $length = 512 * 1024; // 512 kB
        while (!feof($inFile)) {
            gzwrite($gzFile, fread($inFile, $length));
        }
        
        // Close files
        fclose($inFile);
        gzclose($gzFile);
        
        // Return the new filename
        return $gzFilename;
    }
    
    // UTIL
    public function __toString()
    {
        return get_class( $this).'['.$this->_path.']['.$this->_prefix.']['.$this->_daysToArchive.']['.$this->_daysToDelete.']';
    }
}