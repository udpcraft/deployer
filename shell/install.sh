#!/bin/sh
## Init Script

if [ `whoami` != 'root' ] ; then
    echo 'use root or sudo to install server'
    exit 1
fi

DEPLOYER_ROOT="/opt/udp-deployer"
SOURCE_DIR=$(cd "$(dirname "$0")/../"; pwd)
DEPLOY_USER="deployer"
LOG_ROOT=$DEPLOYER_ROOT"/log"
LOG_DIRS="runtime"

nginx_log_dir=$DEPLOYER_ROOT"/envs/local/nginx/logs"
fpm_log_dir=$DEPLOYER_ROOT"/envs/local/php/Log"
fpm_var_dir=$DEPLOYER_ROOT"/envs/local/php/var/run"
# add user
useradd $DEPLOY_USER

# copy source to DEPLOYER_ROOT
if (test ! -d $DEPLOYER_ROOT)
then
    cp -fr $SOURCE_DIR"/" $DEPLOYER_ROOT"/"
fi

chown -R $DEPLOY_USER:$DEPLOY_USER $DEPLOYER_ROOT

# create init.d Script
if (test ! -f "/etc/init.d/deployer")
then
    cp $SOURCE_DIR"/shell/deployer" /etc/init.d
    chmod +x /etc/init.d/deployer
fi

# update nginx and fpm binary file execute permissions
chmod +x $DEPLOYER_ROOT"/envs/local/php/sbin/php-fpm"
chmod +x $DEPLOYER_ROOT"/envs/local/nginx/sbin/nginx"
chmod +x $DEPLOYER_ROOT"/shell/ssh_proxy.sh"

# init deployer env
if (test ! -f $nginx_log_dir)
then
    mkdir -p $nginx_log_dir
fi

if (test ! -f $fpm_log_dir)
then
    mkdir -p $fpm_log_dir
fi

if (test ! -f $fpm_var_dir)
then
    mkdir -p $fpm_var_dir
fi

# init app env
echo
echo create application environment
echo "++++++++++++++++++++++++++++++++++++++++++++"
echo

if (test ! -d $LOG_ROOT)
then
    mkdir -p $LOG_ROOT
fi

cd $LOG_ROOT

for dir in $LOG_DIRS
do
    if (test ! -d $dir)
    then
        mkdir -p $dir
        chmod -R 777 $dir
        echo mkdir $dir ................ OK
    fi
done

# init app env config
cp -f $DEPLOYER_ROOT"/web/config/env.dist" $DEPLOYER_ROOT"/web/.env"

echo
echo "++++++++++++++++++++++++++++++++++++++++++++"
