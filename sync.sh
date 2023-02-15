#!/bin/bash

# Change all of the instances of llpc to lpc
echo "Change all of the instances of llpc to lpc"
b=($(find . -type f | xargs grep -l llpc 2>/dev/null))
for a in ${b[@]}
do
    sed 's/llpc/lpc/g' $a > $a.new
    mv $a.new $a
done

echo "rsync with the Tinicum DB VM"
# rsync with the Tinicum DB VM
rsync -av -e ssh . root@tinicumDB:/var/www/lpc.tinicumconservancy.org/public_html --exclude '.git' --exclude 'sync.sh'

echo "Changing the PermitRootLogin back to no on remote VM"
ssh root@tinicumDB "sed 's/PermitRootLogin yes/PermitRootLogin no/g' /etc/ssh/sshd_config > /etc/ssh/sshd_config.new; mv /etc/ssh/sshd_config.new /etc/ssh/sshd_config; systemctl restart sshd"

echo "Resetting the local files to use llpc"
for a in ${b[@]}
do
    git restore $a
done
