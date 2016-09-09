## ssh proxy,disable StrictHostKeyChecking

ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no $*


