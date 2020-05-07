#!/usr/bin/perl
=head
    File    : myauth.pl
    Time    : 2020/05/07 01:28:00
    Author  : Guo,Xuan-Chen
=cut
use strict;
use warnings;
use feature qw(say switch);
use Data::Dumper qw(Dumper);
use Crypt::PasswdMD5;
use IO::File;

my $username = <STDIN>; chomp($username);
my $password = <STDIN>; chomp($password);

my $rootDirectory = "/etc/apache2/script/";
my $logsPath = "${rootDirectory}logs/";
my $lockAuth = "${rootDirectory}lockAuth.txt";

my $date = `date +"%b %d %H:%M:%S"`; chomp($date);
my $fh = IO::File->new();
$fh->open("${rootDirectory}.htpasswd");

while(my $line = $fh->getline)
{
	chomp($line);
	my ($authUsername, $authPassword) = split(/:/, $line);
	if($authUsername eq $username)
	{
		my ($space, $cryptType, $salt, $result) = split(/\$/, $authPassword);
		my $hashPassword = apache_md5_crypt($password, $salt);
		my $lockJudge = `cat ${lockAuth} | grep -w ${username}`; chomp($lockJudge);
		my @lockUserInfo = split(/ /, $lockJudge);
		
		if($hashPassword eq $authPassword)
		{
			if($lockJudge ne "")
			{
				if($lockUserInfo[1] == 3)
				{
					system("echo \"[${date}] - ${username} login web is locked\" >> ${logsPath}${username}.log");
					sleep(3);
					exit(1);
				}
				system("sed -i '/${username}/d' ${lockAuth}");

			}
			system("echo \"[${date}] - ${username} login web is success\" >> ${logsPath}${username}.log");
			exit(0)
		}
		else
		{
			if($lockJudge eq "")
			{
				system("echo \"${username} 1\" >> ${lockAuth}");
			}
			else
			{
				if(3 > $lockUserInfo[1])
				{
					my $lockNewCount = $lockUserInfo[1] + 1;
					system("sed -i 's/${lockJudge}/${username} ${lockNewCount}/' ${lockAuth}");
					if($lockNewCount == 3)
					{
						system("echo \"${username}\" | ${rootDirectory}delLock.pl &");
					}
				}
			} 	 
			system("echo \"[${date}] - ${username} login web is failure\" >> ${logsPath}${username}.log");
			sleep(3);
			exit(1);
		}
	}
}
system("echo \"[${date}] - ${username} login web is failure\" >> ${logsPath}${username}.log");
$fh->close;
sleep(3);
exit(1);