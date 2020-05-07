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

my $username = <STDIN>; chomp($username);
sleep(60);
system("sed -i '/$username 3/d' /etc/apache2/script/lockAuth.txt");