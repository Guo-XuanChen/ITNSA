#!/usr/bin/perl

use strict;
use warnings;
use Switch;
use feature qw(say);
use Data::Dumper qw(Dumper);

my @argv = @ARGV;
my $action = $argv[0];
my $clientIP = $argv[1];
my $clientMac = $argv[2];
my $hostName = $argv[3];
my $leaseTime = $argv[4];
my @clientMac = split(":", $clientMac);
my $clientMacFormat = join(":", map({ sprintf "%02s", $_ } @clientMac));

switch($action){
	case "commit" {
		system("echo \"$clientIP $clientMacFormat $hostName $leaseTime\" >> /var/log/dhcpd/commit.log");
		#system("logger \"commit\"");
	}
	case "release" {
		system("echo \"$clientIP $clientMacFormat $hostName\" >> /var/log/dhcpd/release.log");
		#system("logger \"release\"");
	}
	case "expiry" {
		system("echo \"$clientIP $clientMacFormat $hostName\" >> /var/log/dhcpd/expiry.log");
		#system("logger \"expiry\"");
	}
	else { 
		# /* This is default rule */
	}
}
