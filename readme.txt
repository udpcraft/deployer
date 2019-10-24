=== readme ===

1 use root or sudo to run `install.sh`
  shell/install.sh

2 start deployer service
  /etc/init.d/deployer start

3 add deloyer to sudoers

4 grant user deploer can access git repos

default administrator user id admin/admin

roles：
project，host，host-group，env，release，review，publish

flow：
1. project manager
  add （name，dev，lang，desc，path，source[git|svn|...]，status, add_user, up_user, add_time, up_time）
  edit
  delete
  list
2. host manager
  add (name, ip, idc, tag, status, add_user, up_user, add_time, up_time)
  edit
  delete
  list
3. env manager
  config [dev, test, pre, prod]
4 release
  1) select project
  2) checkout|pull code 
  3) diff
  4) send review
  5) lock project relase
  6) create tag
5 review
  1) list reviews
  2) show review
  3) confirm review
  4) refuse riview
  5) rollback tag
  6) lock project
  7) create pre publish, update project status
6 publish
  1) list publish by user
  2) show diff
  3) publish
  4) to publish log
  5) send webhook: test, monitor,...
  
  
