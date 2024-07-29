# Virtuozzo Hybrid Server 9 WHMCS VPS Module Guide

The WHMCS VPS module works with the WHMCS module to maintain synchronization between virtual servers created by users in Virtuozzo Hybrid Server 9 (not in the WHMCS module) and products created in WHMCS. This enables you to map/unmap those virtual machines and perform billing operations and general management tasks for them using the Virtuozzo Hybrid Server 9 WHMCS module. It also offers an automated way of building virtual servers after the initial purchase. The product can be set to automatically build based on the setting on the products page. There are a few different options for building at certain times in the billing process.

**Basic Functionality:**
- Create
- Terminate
- Upgrade/Downgrade

**Product Configuration:**
- Synchronization Of Templates
- Static Resources Per Product:
  - Resources (Memory, CPU, CPU Shares, Disk Size, SWAP Space)
  - OS Template (Template Group, OS Template)
  - Storage/Backups (Data Store Zone, SWAP: Data Store Zone, Primary Disk Min Iops, Swap Disk Min Iops, Max Disk Size)
  - Networks (Compute Zone, Compute resource, IP Addresses, Max Port Speed, Network Zone)
  - Additionals (VM Description, Type Of Filesystem, Licensing Server ID, Licensing Type, Licensing Key, Automatic Backup, Virtual Machine Build, Use HTML5 Console)
  - User Configuration (User Role, User Billing Plan, User Group)
  - Up & Down Autoscaling (RAM, CPU, Disk)
  - Client Area Action Allowed
- Configurable Options (Memory, CPU, Primary Disk Size, Swap Disk Size, Extra IP Address, Port Speed, CPU Priority, OS Template, Primary Data Store, Swap Data Store, Network Group)

**Client Area Features:**
- Manage VM Status (Start, Stop, Shutdown, Reboot, Startup On Recovery, Rebuild, Lock/Unlock)
- View VM Details & Status
- Firewall Management
- IP Management
- Network Management
- CPU Usage Graphs
- Disk Management
- Backups & Backups Schedule
- Auto Scaling
- Activity Logs

**Additionally:**
- Supports Virtuozzo Hybrid Server 9 Billing For WHMCS
- Supports WHMCS V6 and Later
