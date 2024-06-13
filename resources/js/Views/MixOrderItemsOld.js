import React, { useMemo, useState, useEffect } from 'react';
import axios from 'axios';
import { useTable, usePagination, useFilters } from 'react-table';
import 'bootstrap/dist/css/bootstrap.min.css';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faShoppingCart } from '@fortawesome/free-solid-svg-icons';
import { Link } from 'react-router-dom';

const DataTable = () => {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectAll, setSelectAll] = useState(false);
  const [selectedRows, setSelectedRows] = useState([]);
  const [firstPageIndex, setFirstPageIndex] = useState(0);
  const [filterCategory, setFilterCategory] = useState('');
  const [filterName, setFilterName] = useState('');
  const [cartItems, setCartItems] = useState([]);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await axios.get('/positems');
        const rdata = response.data.items;
        setData(rdata);
        setLoading(false);
      } catch (error) {
        console.error('Error fetching data:', error);
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  useEffect(() => {
    setSelectedRows([]);
  }, [data]);

  const handleSelectAll = (e) => {
    const checked = e.target.checked;
    setSelectAll(checked);
    if (checked) {
      setSelectedRows([...filteredData]);
      setCartItems([...filteredData]);
    } else {
      setSelectedRows([]);
      setCartItems([]);
    }
  };

  const handleSelectRow = (e, row) => {
    const checked = e.target.checked;
    const selectedRow = row.original;

    if (checked) {
      setSelectedRows((prevSelectedRows) => [...prevSelectedRows, selectedRow]);
      setCartItems((prevCartItems) => [...prevCartItems, selectedRow]);
    } else {
      setSelectedRows((prevSelectedRows) =>
        prevSelectedRows.filter((item) => item.item_id !== selectedRow.item_id)
      );
      setCartItems((prevCartItems) =>
        prevCartItems.filter((item) => item.item_id !== selectedRow.item_id)
      );
    }
  };

  const handleCategoryChange = (e) => {
    setFilterCategory(e.target.value);
  };

  const handleNameFilterChange = (e) => {
    setFilterName(e.target.value);
  };

  const filteredData = useMemo(() => {
    let filtered = data;
    if (filterCategory) {
      filtered = filtered.filter((item) => item.category === filterCategory);
    }
    if (filterName) {
      filtered = filtered.filter((item) =>
        item.name.toLowerCase().includes(filterName.toLowerCase())
      );
    }
    return filtered;
  }, [data, filterCategory, filterName]);

  const uniqueCategories = useMemo(() => {
    const categories = data.map((item) => item.category);
    return Array.from(new Set(categories));
  }, [data]);

  const columns = useMemo(
    () => [
      {
        Header: (
          <input
            type="checkbox"
            onChange={handleSelectAll}
            checked={selectAll}
          />
        ),
        id: 'checkbox',
        Cell: ({ row }) => (
          <input
            type="checkbox"
            onChange={(e) => handleSelectRow(e, row)}
            checked={selectedRows.some(
              (selectedRow) => selectedRow.item_id === row.original.item_id
            )}
          />
        ),
      },
      { Header: 'ID', accessor: 'item_id' },
      { Header: 'Name', accessor: 'name' },
      { Header: 'Item_No', accessor: 'item_number' },
      { Header: 'Category', accessor: 'category' },
      { Header: 'Quantity', accessor: 'receiving_quantity' },
    ],
    [selectedRows, selectAll]
  );

  const {
    getTableProps,
    getTableBodyProps,
    headerGroups,
    page,
    nextPage,
    previousPage,
    canNextPage,
    canPreviousPage,
    state: { pageIndex, pageSize },
    gotoPage,
    pageCount,
    prepareRow,
    setPageSize,
    setFilter,
  } = useTable(
    {
      columns,
      data: filteredData,
      initialState: { pageIndex: 0, pageSize: 10 },
    },
    useFilters,
    usePagination
  );

  const handlePageSizeChange = (e) => {
    const newSize = Number(e.target.value);
    setPageSize(newSize);
    if (pageIndex >= Math.ceil(filteredData.length / newSize)) {
      gotoPage(Math.ceil(filteredData.length / newSize) - 1);
    }
  };

  useEffect(() => {
    setFirstPageIndex(pageIndex);
  }, [pageIndex]);

  useEffect(() => {
    console.log('Cart Items:', cartItems);
  }, [cartItems]);

  return (
    <div className="inner_bg">
      <div id="wrapper">
        <header id="header" className="clearfix">
          <nav className="inner_nav">
            <ul>
              <li><Link to={`/home`}>Main Menu</Link></li>
              <li>»</li>
              <li><Link to={`/categories`}>Events</Link></li>
              <li>»</li>
              <li>»</li>
              <li>Place Order</li>
            </ul>
          </nav>
          <div className="logo"> <a href="#"><img src="/images/logo.png" alt="" /></a> </div>
        </header>
        <section id="body">
          <div className="inner_container">
            <div className="place_order_container clearfix">
              <h3>Select Product</h3>
              <form>
                <div className="place_order_form">
                  <div className="container">
                    {loading ? (
                      <div>Loading...</div>
                    ) : (
                      <div>
                        <div className="d-flex mb-3 align-items-center">
                          <div className="flex-grow-1">
                            <input
                              type="text"
                              value={filterName}
                              onChange={handleNameFilterChange}
                              placeholder="Search by name"
                              className="form-control-sm mb-2"
                            />
                          </div>
                          <div className="flex-grow-0">
                            <select
                              value={pageSize}
                              onChange={handlePageSizeChange}
                              className="form-select-sm mb-2"
                            >
                              <option value={10}>10</option>
                              <option value={25}>25</option>
                              <option value={50}>50</option>
                              <option value={100}>100</option>
                            </select>
                          </div>
                          <div className="flex-grow-0 ml-2">
                            <button
                              className="btn btn-primary btn-sm"
                            >
                              <FontAwesomeIcon icon={faShoppingCart} />{' '}
                              {cartItems.length}
                            </button>
                          </div>
                        </div>
                        <select
                          value={filterCategory}
                          onChange={handleCategoryChange}
                          className="form-select-sm mb-2"
                        >
                          <option value="">All Categories</option>
                          {uniqueCategories.map((category, index) => (
                            <option key={index} value={category}>
                              {category}
                            </option>
                          ))}
                        </select>
                        {filteredData.length === 0 ? (
                          <div className='text-center'>No items record</div>
                        ) : (
                          <table {...getTableProps()} className="table table-striped">
                            <thead>
                              {headerGroups.map((headerGroup) => (
                                <tr {...headerGroup.getHeaderGroupProps()}>
                                  {headerGroup.headers.map((column, index) => (
                                    <th {...column.getHeaderProps()} key={index}>
                                      {column.render('Header')}
                                    </th>
                                  ))}
                                </tr>
                              ))}
                            </thead>
                            <tbody {...getTableBodyProps()}>
                              {page.map((row) => {
                                prepareRow(row);
                                return (
                                  <tr {...row.getRowProps()}>
                                    {row.cells.map((cell, index) => (
                                      <td {...cell.getCellProps()} key={index}>
                                        {cell.render('Cell')}
                                      </td>
                                    ))}
                                  </tr>
                                );
                              })}
                            </tbody>
                          </table>
                        )}
                        <div className="d-flex justify-content-between">
                          <div>
                            <button
                              onClick={() => {
                                if (pageIndex > 0) {
                                  setFirstPageIndex(pageIndex - 1);
                                  previousPage();
                                }
                              }}
                              disabled={!canPreviousPage}
                              className="btn btn-primary"
                            >
                              Previous
                            </button>
                            {Array.from(
                              { length: Math.min(pageCount - firstPageIndex, 3) },
                              (_, i) => (
                                <button
                                  key={i + firstPageIndex}
                                  onClick={() => {
                                    gotoPage(firstPageIndex + i);
                                    setFirstPageIndex(firstPageIndex + i);
                                  }}
                                  className={`btn btn-primary mx-1 ${
                                    pageIndex === i + firstPageIndex ? 'active' : ''
                                  }`}
                                >
                                  {i + firstPageIndex + 1}
                                </button>
                              )
                            )}
                            <button
                              onClick={() => {
                                if (pageIndex < pageCount - 1) {
                                  setFirstPageIndex(pageIndex + 1);
                                  nextPage();
                                }
                              }}
                              disabled={!canNextPage}
                              className="btn btn-primary"
                            >
                              Next
                            </button>
                          </div>
                          <div>
                            Page{' '}
                            <strong>
                              {pageIndex + 1} of {pageCount}
                            </strong>
                          </div>
                        </div>
                        <div>
                          <p>
                            Showing {pageIndex * pageSize + 1}-
                            {Math.min((pageIndex + 1) * pageSize, filteredData.length)} of{' '}
                            {filteredData.length} rows
                          </p>
                        </div>
                      </div>
                    )}
                  </div>
                </div>
              </form>
            </div>
          </div>
        </section>
      </div>
    </div>
  );
};

export default DataTable;
