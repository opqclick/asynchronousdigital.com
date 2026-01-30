# Digital Ocean Spaces Configuration

This document describes the Digital Ocean Spaces cloud storage setup for the Asynchronous Digital CRM system.

## Configuration Details

- **Bucket Name**: `asynchronousdigitalcloudstorage`
- **Region**: `sgp1` (Singapore)
- **Endpoint**: `https://sgp1.digitaloceanspaces.com`
- **CDN URL**: `https://asynchronousdigitalcloudstorage.sgp1.digitaloceanspaces.com`
- **Root Folder**: `AsynchronousDigitalCRM` (All files are stored in this folder)

## File Storage Structure

All uploaded files are stored in Digital Ocean Spaces with the following folder structure:

```
asynchronousdigitalcloudstorage/
└── AsynchronousDigitalCRM/
    ├── profile_pictures/     # User profile photos
    ├── user_documents/       # User documents (ID, contracts, etc.)
    ├── projects/            # Project attachments
    └── tasks/               # Task attachments
```

## Environment Variables

The following environment variables have been configured in your `.env` file:

```env
FILESYSTEM_DISK=do_spaces
DO_SPACES_KEY=          # ⚠️ You need to add your Digital Ocean Spaces Access Key
DO_SPACES_SECRET=       # ⚠️ You need to add your Digital Ocean Spaces Secret Key
DO_SPACES_REGION=sgp1
DO_SPACES_BUCKET=asynchronousdigitalcloudstorage
DO_SPACES_ENDPOINT=https://sgp1.digitaloceanspaces.com
DO_SPACES_URL=https://asynchronousdigitalcloudstorage.sgp1.digitaloceanspaces.com
DO_SPACES_ROOT=AsynchronousDigitalCRM
```

## Required Action: Add Your API Keys

⚠️ **IMPORTANT**: You need to add your Digital Ocean Spaces API credentials to the `.env` file.

1. Log in to your Digital Ocean account
2. Navigate to: API → Spaces Keys (or Spaces → Manage Keys)
3. Generate a new Spaces access key if you don't have one
4. Copy your **Access Key** and **Secret Key**
5. Update the `.env` file:
   ```env
   DO_SPACES_KEY=your_actual_access_key_here
   DO_SPACES_SECRET=your_actual_secret_key_here
   ```

## Package Installed

The AWS SDK for PHP has been installed via Composer:
- `league/flysystem-aws-s3-v3`: ^3.31

This package provides S3-compatible storage drivers that work with Digital Ocean Spaces.

## Updated Files

### Controllers
All file upload controllers have been updated to use `do_spaces` disk:

1. **UserController** (`app/Http/Controllers/Admin/UserController.php`)
   - Profile pictures → `profile_pictures/` folder
   - Documents → `user_documents/` folder

2. **ProjectController** (`app/Http/Controllers/Admin/ProjectController.php`)
   - Project attachments → `projects/` folder
   - Stores metadata: filename, path, size, upload timestamp

3. **TaskController** (`app/Http/Controllers/Admin/TaskController.php`)
   - Task attachments → `tasks/` folder
   - Stores metadata: filename, path, size, upload timestamp

### Views
Updated to display files from Digital Ocean Spaces:

- **User Show View** (`resources/views/admin/users/show.blade.php`)
  - Profile picture displays from Digital Ocean
  - Document links point to Digital Ocean URLs

### Configuration
- **Filesystem Config** (`config/filesystems.php`)
  - Added `do_spaces` disk configuration
  - Set as default filesystem disk

## Features

### User Management
- ✅ Profile picture upload (max 2MB)
- ✅ Multiple document uploads (max 5MB per file)
- ✅ Files stored in Digital Ocean Spaces
- ✅ Download links for all documents

### Project Management
- ✅ Multiple file attachments (max 10MB per file)
- ✅ Supported formats: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, ZIP
- ✅ File metadata stored in database
- ✅ New files append to existing attachments

### Task Management
- ✅ Multiple file attachments (max 10MB per file)
- ✅ Same file format support as projects
- ✅ File metadata stored in database

## Testing the Setup

After adding your API keys to `.env`:

1. **Test User Upload**:
   - Go to Users → Create User
   - Upload a profile picture
   - Upload one or more documents
   - Save and view the user profile
   - Verify images and download links work

2. **Test Project Upload**:
   - Go to Projects → Create Project
   - Add project details and upload files
   - Save and verify files are stored

3. **Test Task Upload**:
   - Go to Tasks → Create Task
   - Add task details and upload files
   - Save and verify files are stored

## File Access

All files are publicly accessible via CDN URLs:
```
https://asynchronousdigitalcloudstorage.sgp1.digitaloceanspaces.com/AsynchronousDigitalCRM/[folder]/[filename]
```

## Security Notes

- Keep your `DO_SPACES_KEY` and `DO_SPACES_SECRET` private
- Never commit the `.env` file to version control
- Consider setting up CORS policies in Digital Ocean if needed
- Files are currently publicly accessible - consider implementing signed URLs for sensitive documents

## Troubleshooting

### Common Issues

1. **"Class 'League\Flysystem\AwsS3V3\AwsS3V3Adapter' not found"**
   - Run: `composer require league/flysystem-aws-s3-v3`

2. **"Access Denied" errors**
   - Verify your `DO_SPACES_KEY` and `DO_SPACES_SECRET` are correct
   - Check that your Spaces key has read/write permissions

3. **"Bucket not found"**
   - Verify the bucket name is exactly: `asynchronousdigitalcloudstorage`
   - Check the region is: `sgp1`

4. **Files not uploading**
   - Clear config cache: `php artisan config:clear`
   - Check file size limits in forms match validation rules

5. **Images not displaying**
   - Verify files are in the bucket via Digital Ocean console
   - Check CORS settings if accessing from different domain

## Next Steps

Once your API keys are configured and tested:

1. ✅ All user uploads will go to Digital Ocean Spaces
2. ✅ All project/task files will be stored in the cloud
3. ✅ Files are accessible via CDN for fast delivery
4. Consider implementing:
   - File deletion functionality
   - Signed URLs for private documents
   - Image thumbnail generation
   - File preview for images/PDFs
   - Attachment lists in project/task show views

## Support

For Digital Ocean Spaces documentation:
- https://docs.digitalocean.com/products/spaces/

For Laravel filesystem documentation:
- https://laravel.com/docs/filesystem
