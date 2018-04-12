-- ###################################### --
-- ##     iptables ICMP Log Database   ## --
-- #   File: iptablesIcmpDBCreate.sql   # --
-- #   Time: 2018/04/11 19:50:00        # --
-- #   Author: Guo,Xuan-Chen            # --
-- ###################################### --

-- create database
create database iptables_icmplog;

-- create tables
create table iptables_icmplog.iptables_icmplog (
    id integer auto_increment primary key,
    time varchar(255) not null,
    hostname varchar(255) not null,
    in_if varchar(255),
    out_if varchar(255),
    src_ip varchar(255),
    dst_ip varchar(255),
    proto varchar(255),
    ttl tinyint unsigned 
);
