import React, { useState, useEffect } from 'react';
import { Table, Input, message, Spin } from 'antd';
import axios from 'axios';

const { Search } = Input;

const ApplicationTable = () => {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [searchTerm, setSearchTerm] = useState('');

  useEffect(() => {
    fetchApplications();
  }, []);

  const fetchApplications = async () => {
    try {
      const response = await axios.get(
        'https://raw.githubusercontent.com/RashitKhamidullin/Educhain-Assignment/refs/heads/main/applications'
      );
      setData(response.data);
    } catch (error) {
      setError('Failed to load data');
      message.error('Failed to load applications');
    } finally {
      setLoading(false);
    }
  };

  const handleSearch = (value) => {
    setSearchTerm(value.toLowerCase());
  };

  const filteredData = data.filter(
    (item) =>
      item.applicantName.toLowerCase().includes(searchTerm) ||
      item.status_En.toLowerCase().includes(searchTerm) ||
      item.studentID.toLowerCase().includes(searchTerm)
  );

  const columns = [
    {
      title: 'Application No',
      dataIndex: 'applicationNO',
      sorter: (a, b) => a.applicationNO - b.applicationNO,
    },
    {
      title: 'Applicant Name',
      dataIndex: 'applicantName',
      sorter: (a, b) => a.applicantName.localeCompare(b.applicantName),
    },
    {
      title: 'Application Date',
      dataIndex: 'applicationDate',
      sorter: (a, b) => new Date(a.applicationDate) - new Date(b.applicationDate),
    },
    {
      title: 'Student ID',
      dataIndex: 'studentID',
    },
    {
      title: 'Paid Amount',
      dataIndex: 'paidAmount',
    },
    {
      title: 'Status (English)',
      dataIndex: 'status_En',
    },
    {
      title: 'Status (Arabic)',
      dataIndex: 'status_Ar',
    },
    {
      title: 'Last Updated',
      dataIndex: 'lastDate',
    },
  ];

if (loading)
  return (
    <div style={{ textAlign: 'center', marginTop: 50 }}>
      <Spin tip="Loading applications..." />
    </div>
  );
  if (error) return <p>{error}</p>;

  return (
    <div>
      <Search
        placeholder="Search by Applicant Name, Status, or Student ID"
        onSearch={handleSearch}
        style={{ marginBottom: 20, width: 300 }}
      />
      <Table
        columns={columns}
        dataSource={filteredData}
        rowKey="applicationNO"
        pagination={{ pageSize: 10 }}
      />
    </div>
  );
};

export default ApplicationTable;
