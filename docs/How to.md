How to upload files from home to web server (gregvinall.com)
    1. compress files at home
        - cd ~/Laravel/strearch
        - tar -cvjf strearch.bz2 *
        - copy strearch.bz2 to the webserver using filezilla
    2. ssh to web server
        - ssh2gsv
        - cd ~/Laravel/strearch
    3. uncompress the file on the web server
        - tar -xvf strearch.bz2
    4. tidy up
        - rm ~/Laravel/strearch/strearch.bz2 (and confirm)

