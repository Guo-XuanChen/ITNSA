#!/usr/bin/perl
use strict;
use warnings;
use feature qw(say switch);

my $contents = `tail -n 1 /var/log/tac_plus.acct`; chop($contents);

system("mkdir -p /var/log/aaa/monitor/");
if($contents =~ /priv-lvl=([0-9]{1}[0-5]{0,1})/){
	my $commandLevel = sprintf("%02d", $1);
	system("echo \"$contents\" >> /var/log/aaa/monitor/comm$commandLevel.log");
}else{
	if($contents =~ /start/){
		system("echo \"$contents\" >> /var/log/aaa/monitor/login.log");
	}elsif($contents =~ /stop/){
		system("echo \"$contents\" >> /var/log/aaa/monitor/logout.log");

	}
}


