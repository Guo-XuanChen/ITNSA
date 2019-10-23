# Linux TACACS+
> OS: Linux, Programming: Perl

## # install package
```go=
# apt install tacacs+ 
```

## # Feature 1 - Using an External Script for Authorization
* **authorizationManagement.pl**

## # Feature 2 - Accounting Log (Monitoring)
* **tacacsMonitor.pl**
```go=
# apt install incron
# echo "root" > /etc/incron.allow
# incrontab -e
/var/log/tac_plus.acct IN_MODIFY,IN_NO_LOOP /usr/bin/perl /script/perl/tacacsMonitor.pl 
```

