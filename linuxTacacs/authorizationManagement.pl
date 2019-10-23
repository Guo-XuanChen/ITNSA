#!/usr/bin/perl
use strict;
use warnings;
use feature qw(say switch);

# /* argv */
my @argv = @ARGV;

my $date = `date +"%Y-%m-%d %H-%M-%S"`;
chop($date);

# Success or Failure log 
sub authorizationLog {
	my @argv = @_;
	my $filename = lc($argv[1]);
	system("mkdir -p /var/log/aaa/authorization/");
	system("echo \"$date $argv[0] Authorization $argv[1]\" >> /var/log/aaa/authorization/$filename.log");
}

if($argv[0] eq "user01"){
	if($argv[1] =~ /^192\.168\.1\.[0-9]$/){
		say "priv-lvl=1";
		say "cli-view-name=interface_view";
		authorizationLog($argv[0], "Success");
		exit(2);
	}elsif($argv[1] eq "192.168.1.20"){
		say "priv-lvl=15";	
		authorizationLog($argv[0], "Success");
		exit(2);
	}else{
		say STDERR "% Permission Denied";
		authorizationLog($argv[0], "Failure");
		exit(3);
	}
}

if($argv[0] eq "user02"){
	if($argv[1] =~ /^192\.168\.1\.[1][1-9]$/){
		say "priv-lvl=1";
		say "cli-view-name=line_view";
		authorizationLog($argv[0], "Success");
		exit(2);
	}elsif($argv[1] eq "192.168.1.1"){
		say "priv-lvl=15";
		authorizationLog($argv[0], "Success");
		exit(2);
	}else{
		say STDERR "% Permission Denied";
		authorizationLog($argv[0], "Failure");
		exit(3);
	}
}
