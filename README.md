```sh
curl -L https://github.com/PaulBalanche/wpextend-cli/archive/refs/heads/master.zip > wpe-cli.zip
unzip wpe-cli.zip
chmod +x wpextend-cli-master/index.php
rm -r /usr/local/lib/wpextend-cli
mv wpextend-cli-master /usr/local/lib/wpextend-cli
ln -s /usr/local/lib/wpextend-cli/index.php /usr/local/bin/wpe
```