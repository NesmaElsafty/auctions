<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TryController extends Controller
{
    /**
     * Read content from the text.txt file
     */
    public function readTextFile(Request $request)
    {
        try {

            $filePath = public_path($request->input('filepath'));
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }
            
            $content = file_get_contents($filePath);
            
            return response()->json([
                'success' => true,
                'content' => $content,
                'message' => 'File read successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reading file: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Write content to the text.txt file
     */
    public function writeTextFile(Request $request)
    {
        try {
            $request->validate([
                'content' => 'required|string'
            ]);
            
            $filePath = public_path('contracts/text.txt');
            $content = $request->input('content');
            
            // Create directory if it doesn't exist
            $directory = dirname($filePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            $result = file_put_contents($filePath, $content);
            
            if ($result === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to write to file'
                ], 500);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'File written successfully',
                'bytes_written' => $result
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error writing file: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Append content to the text.txt file
     */
    public function appendTextFile(Request $request)
    {
        try {
            $request->validate([
                'content' => 'required|string'
            ]);
            
            $filePath = public_path('contracts/text.txt');
            $content = $request->input('content');
            
            // Create directory if it doesn't exist
            $directory = dirname($filePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            $result = file_put_contents($filePath, $content, FILE_APPEND | LOCK_EX);
            
            if ($result === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to append to file'
                ], 500);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Content appended successfully',
                'bytes_written' => $result
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error appending to file: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate a new file with custom name and type
     */
    public function generateNewFile(Request $request)
    {
        try {
            $request->validate([
                'filename' => 'required|string|max:255',
                'filetype' => 'required|string|max:10',
                'content' => 'nullable|string',
                'directory' => 'nullable|string|max:255'
            ]);
            
            $filename = $request->input('filename');
            $filetype = $request->input('filetype');
            $content = $request->input('content', '');
            $directory = $request->input('directory', 'contracts');
            
            // Sanitize filename - remove any path separators and dangerous characters
            $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
            
            // Sanitize filetype - remove any dangerous characters
            $filetype = preg_replace('/[^a-zA-Z0-9]/', '', $filetype);
            
            // Sanitize directory - remove any dangerous path characters
            $directory = preg_replace('/[^a-zA-Z0-9\/_-]/', '_', $directory);
            
            // Ensure filetype starts with a dot
            if (!str_starts_with($filetype, '.')) {
                $filetype = '.' . $filetype;
            }
            
            // Create full file path
            $filePath = public_path($directory . '/' . $filename . $filetype);
            
            // Create directory if it doesn't exist
            $dirPath = dirname($filePath);
            if (!is_dir($dirPath)) {
                if (!mkdir($dirPath, 0755, true)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to create directory'
                    ], 500);
                }
            }
            
            // Check if file already exists
            if (file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File already exists',
                    'file_path' => $directory . '/' . $filename . $filetype
                ], 409);
            }
            
            // Write content to file
            $result = file_put_contents($filePath, $content);
            
            if ($result === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create file'
                ], 500);
            }
            
            // Get file info
            $fileInfo = [
                'filename' => $filename . $filetype,
                'file_path' => $directory . '/' . $filename . $filetype,
                'full_path' => $filePath,
                'size' => filesize($filePath),
                'created_at' => date('Y-m-d H:i:s', filectime($filePath))
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'File created successfully',
                'file_info' => $fileInfo,
                'bytes_written' => $result
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating file: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * List all files in a directory
     */
    public function listFiles(Request $request)
    {
        try {
            $directory = $request->input('directory', 'try');
            
            // Sanitize directory path
            $directory = preg_replace('/[^a-zA-Z0-9\/_-]/', '_', $directory);
            
            $dirPath = public_path($directory);
            
            if (!is_dir($dirPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Directory not found'
                ], 404);
            }
            
            $files = [];
            $items = scandir($dirPath);
            
            foreach ($items as $item) {
                if ($item !== '.' && $item !== '..') {
                    $itemPath = $dirPath . '/' . $item;
                    if (is_file($itemPath)) {
                        $files[] = [
                            'name' => $item,
                        ];
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'directory' => $directory,
                'files' => $files,
                'count' => count($files)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error listing files: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete a file
     */
    public function deleteFile(Request $request)
    {
        try {
            $request->validate([
                'filepath' => 'required|string'
            ]);
            
            $filePath = $request->input('filepath');
            
            // Sanitize file path to prevent directory traversal
            $filePath = preg_replace('/[^a-zA-Z0-9\/._-]/', '_', $filePath);
            
            // Ensure the path is within the public directory
            $fullPath = public_path($filePath);
            $publicPath = public_path();
            
            // Check if the resolved path is within the public directory
            if (!str_starts_with(realpath($fullPath), realpath($publicPath))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file path - access denied'
                ], 403);
            }
            
            // Check if file exists
            if (!file_exists($fullPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }
            
            // Check if it's actually a file (not a directory)
            if (!is_file($fullPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Path is not a file'
                ], 400);
            }
            
            // Get file info before deletion
            $fileInfo = [
                'filename' => basename($fullPath),
                'file_path' => $filePath,
                'size' => filesize($fullPath),
                'created_at' => date('Y-m-d H:i:s', filectime($fullPath)),
                'modified_at' => date('Y-m-d H:i:s', filemtime($fullPath))
            ];
            
            // Delete the file
            if (unlink($fullPath)) {
                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully',
                    'deleted_file' => $fileInfo
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete file'
                ], 500);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting file: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // /**
    //  * Delete multiple files
    //  */
    // public function deleteMultipleFiles(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'filepaths' => 'required|array|min:1',
    //             'filepaths.*' => 'required|string'
    //         ]);
            
    //         $filePaths = $request->input('filepaths');
    //         $results = [];
    //         $successCount = 0;
    //         $errorCount = 0;
            
    //         foreach ($filePaths as $filePath) {
    //             // Sanitize file path
    //             $sanitizedPath = preg_replace('/[^a-zA-Z0-9\/._-]/', '_', $filePath);
                
    //             // Ensure the path is within the public directory
    //             $fullPath = public_path($sanitizedPath);
    //             $publicPath = public_path();
                
    //             // Check if the resolved path is within the public directory
    //             if (!str_starts_with(realpath($fullPath), realpath($publicPath))) {
    //                 $results[] = [
    //                     'filepath' => $filePath,
    //                     'success' => false,
    //                     'message' => 'Invalid file path - access denied'
    //                 ];
    //                 $errorCount++;
    //                 continue;
    //             }
                
    //             // Check if file exists
    //             if (!file_exists($fullPath)) {
    //                 $results[] = [
    //                     'filepath' => $filePath,
    //                     'success' => false,
    //                     'message' => 'File not found'
    //                 ];
    //                 $errorCount++;
    //                 continue;
    //             }
                
    //             // Check if it's actually a file
    //             if (!is_file($fullPath)) {
    //                 $results[] = [
    //                     'filepath' => $filePath,
    //                     'success' => false,
    //                     'message' => 'Path is not a file'
    //                 ];
    //                 $errorCount++;
    //                 continue;
    //             }
                
    //             // Delete the file
    //             if (unlink($fullPath)) {
    //                 $results[] = [
    //                     'filepath' => $filePath,
    //                     'success' => true,
    //                     'message' => 'File deleted successfully'
    //                 ];
    //                 $successCount++;
    //             } else {
    //                 $results[] = [
    //                     'filepath' => $filePath,
    //                     'success' => false,
    //                     'message' => 'Failed to delete file'
    //                 ];
    //                 $errorCount++;
    //             }
    //         }
            
    //         return response()->json([
    //             'success' => $errorCount === 0,
    //             'message' => "Deleted {$successCount} files successfully" . ($errorCount > 0 ? ", {$errorCount} files failed" : ""),
    //             'results' => $results,
    //             'summary' => [
    //                 'total' => count($filePaths),
    //                 'successful' => $successCount,
    //                 'failed' => $errorCount
    //             ]
    //         ]);
            
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error deleting files: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    // get directory list
    public function getDirectoryList(Request $request)
    {
        try {
        // get only directories & ignore storage & . & ..
        $directories = scandir(public_path());
        $directories = array_filter($directories, function($item) {
            return is_dir(public_path($item)) && $item !== 'storage' && $item !== '.' && $item !== '..';
        });
      
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting directory list: ' . $e->getMessage()
            ], 500);
        }
    }
}
