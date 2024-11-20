import os
import urllib.parse

def decode_filenames(directory):
    for filename1 in os.listdir(directory):
        subdirectory1 = os.path.join(directory, filename1)
        if os.path.isdir(subdirectory1):
            for filename2 in os.listdir(subdirectory1):
                subdirectory2 = os.path.join(subdirectory1, filename2)
                if os.path.isdir(subdirectory2):
                    for filename3 in os.listdir(subdirectory2):
                        decoded_filename = urllib.parse.unquote(filename3)
                        
                        old_path = os.path.join(subdirectory2, filename3)
                        new_path = os.path.join(subdirectory2, decoded_filename)

                        # Printing old and new paths for debugging
                        print(f'Old path: {old_path}')
                        print(f'New path: {new_path}')

                        if filename3 != decoded_filename:
                            os.rename(old_path, new_path)
                            print(f'Renamed {filename3} to {decoded_filename}')

directory = "var/www/humoria/recordings (3)"

decode_filenames(directory)
