<?php
            // Pfad ermitteln
            $version_folder = '../typo3_src-12.4.14';
            
            //Symlinks setzen
            symlink($version_folder, 'typo3_src');
            symlink('typo3_src/typo3', 'typo3');
            symlink('typo3_src/index.php', 'index.php');

            //Irgendeine Bestätigung erhalten
            echo "Symlinks wurden erfolgreich erstellt.";
        ?>