#!/bin/sh
alive=$(echo "SELECT COUNT(*) FROM proxies WHERE dead=0" | mysql -h SERVER -u USERNAME '-pPASSWORD DATABASE | tail -n 1)
total=$(echo "SELECT COUNT(*) FROM proxies" | mysql -h SERVER -u USERNAME '-pPASSWORD DATABASE | tail -n 1)
echo $alive of $total proxies are alive.
